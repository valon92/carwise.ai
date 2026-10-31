<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosisSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DiagnosisExportController extends Controller
{
    /**
     * Export diagnosis to PDF
     */
    public function exportToPDF(Request $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'nullable|string|max:255',
                'vehicle' => 'required|array',
                'vehicle.make' => 'required|string|max:100',
                'vehicle.model' => 'required|string|max:100',
                'vehicle.year' => 'required|integer|min:1900|max:'.(date('Y') + 1),
                'vehicle.mileage' => 'nullable|integer|min:0',
                'vehicle.engine_type' => 'nullable|string|max:50',
                'vehicle.engine_size' => 'nullable|string|max:50',
                'symptoms' => 'nullable|array',
                'description' => 'nullable|string',
                'result' => 'required|array',
                'generated_at' => 'nullable|string',
                'session_id' => 'nullable|string|max:64',
            ]);

            $session = null;
            if (! empty($data['session_id'])) {
                $session = DiagnosisSession::where('session_id', $data['session_id'])
                    ->where('user_id', auth()->id())
                    ->with('result')
                    ->first();
            }

            $evidence = $data['result']['evidence'] ?? [];
            if ($session) {
                $meta = is_array($session->ai_response) ? $session->ai_response : [];
                $evidence = array_merge($evidence, [
                    'session_id' => $session->session_id,
                    'diagnosed_at' => optional($session->created_at)?->toIso8601String(),
                    'unlocked_at' => $meta['unlocked_at'] ?? ($evidence['unlocked_at'] ?? null),
                    'ai_provider' => $meta['provider'] ?? ($evidence['ai_provider'] ?? 'openai'),
                    'report_id' => $evidence['report_id']
                        ?? substr(hash('sha256', $session->session_id.'|'.($meta['unlocked_at'] ?? '')), 0, 12),
                ]);

                if (empty($data['result']['illustrations']) && ! empty($meta['illustrations'])) {
                    $data['result']['illustrations'] = $meta['illustrations'];
                }

                foreach ([
                    'hypothesis_disclaimer',
                    'data_quality',
                    'diagnostic_codes_to_check',
                    'next_workshop_questions',
                    'glossary',
                    'diagnostic_engines',
                    'code_refined',
                    'provided_obd_code',
                ] as $field) {
                    if ((empty($data['result'][$field]) || $data['result'][$field] === []) && ! empty($meta[$field])) {
                        $data['result'][$field] = $meta[$field];
                    }
                }

                if (empty($data['result']['provided_obd_code']) && ! empty($meta['refined_obd_code'])) {
                    $data['result']['provided_obd_code'] = $meta['refined_obd_code'];
                }
                if (empty($data['result']['provided_obd_code']) && ! empty($meta['clarifying_answers']['obd_code'])) {
                    $data['result']['provided_obd_code'] = $meta['clarifying_answers']['obd_code'];
                }
                if (empty($evidence['diagnostic_engines']) && ! empty($meta['diagnostic_engines'])) {
                    $evidence['diagnostic_engines'] = $meta['diagnostic_engines'];
                }
            }

            $generatedAt = $data['generated_at']
                ?? ($evidence['unlocked_at'] ?? null)
                ?? now()->toIso8601String();

            $illustrationService = app(\App\Services\DiagnosisIllustrationService::class);

            // Prefer session-stored photorealistic visuals when the client payload is stale.
            if ($session) {
                $meta = is_array($session->ai_response) ? $session->ai_response : [];
                $sessionIllustrations = is_array($meta['illustrations'] ?? null) ? $meta['illustrations'] : [];
                if ($illustrationService->needsRealisticUpgrade($data['result']['illustrations'] ?? [])) {
                    if (! $illustrationService->needsRealisticUpgrade($sessionIllustrations)) {
                        $data['result']['illustrations'] = $sessionIllustrations;
                    } else {
                        $data['result']['illustrations'] = $illustrationService->catalogForResult(
                            $session->result,
                            5,
                            $session
                        );
                    }
                }
            }

            // Same owner logic as Diagnose UI: image + red problem-zone annotation per cause.
            if (! empty($data['result']['likely_causes']) && is_array($data['result']['likely_causes'])) {
                $data['result']['likely_causes'] = $illustrationService->enrichCauses(
                    $data['result']['likely_causes'],
                    is_array($data['result']['illustrations'] ?? null) ? $data['result']['illustrations'] : [],
                    $session
                );

                foreach ($data['result']['likely_causes'] as &$cause) {
                    if (! is_array($cause)) {
                        continue;
                    }
                    $src = $this->illustrationDataUri([
                        'url' => $cause['image_url'] ?? null,
                        'path' => $cause['image_path'] ?? null,
                    ]);
                    if ($src) {
                        $cause['image_src'] = $src;
                    }
                }
                unset($cause);
            }

            // Keep a compact gallery only for causes that have embedded photos (UI parity).
            $illustrations = [];
            foreach (($data['result']['likely_causes'] ?? []) as $cause) {
                if (! is_array($cause) || empty($cause['image_src'])) {
                    continue;
                }
                $illustrations[] = [
                    'src' => $cause['image_src'],
                    'caption' => $cause['annotation']['label']
                        ?? $cause['image_caption']
                        ?? ($cause['title'] ?? $cause['cause'] ?? 'Problem visual'),
                    'meaning' => $cause['annotation']['meaning'] ?? null,
                ];
            }

            $viewData = [
                'title' => $data['title'] ?? 'CarWise.ai — Diagnosis Report',
                'vehicle' => $data['vehicle'],
                'symptoms' => $data['symptoms'] ?? [],
                'description' => $data['description'] ?? '',
                'result' => $data['result'],
                'generated_at' => $generatedAt,
                'evidence' => $evidence,
                'illustrations' => $illustrations,
            ];

            $pdf = Pdf::loadView('diagnosis.export-pdf', $viewData);
            $pdf->setPaper('A4', 'portrait');

            $filename = sprintf(
                'CarWise_Diagnosis_%s_%s_%s.pdf',
                preg_replace('/[^A-Za-z0-9_-]/', '', $data['vehicle']['make']),
                preg_replace('/[^A-Za-z0-9_-]/', '', $data['vehicle']['model']),
                date('Y-m-d_His')
            );

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export diagnosis to JSON
     */
    public function exportToJSON(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'nullable|string|max:255',
                'vehicle' => 'required|array',
                'symptoms' => 'nullable|array',
                'description' => 'nullable|string',
                'result' => 'required|array',
                'generated_at' => 'nullable|string',
                'session_id' => 'nullable|string|max:64',
            ]);

            $filename = 'CarWise_Diagnosis_'
                .($data['vehicle']['make'] ?? 'vehicle').'_'
                .($data['vehicle']['model'] ?? 'model').'_'
                .date('Y-m-d_His').'.json';

            return response()->json([
                'success' => true,
                'data' => $data,
                'filename' => $filename,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating JSON: '.$e->getMessage(),
            ], 500);
        }
    }

    private function illustrationDataUri(array $row): ?string
    {
        $path = $row['path'] ?? null;
        if (is_string($path) && $path !== '' && Storage::disk('public')->exists($path)) {
            $bytes = Storage::disk('public')->get($path);
            $mime = str_ends_with(strtolower($path), '.png') ? 'image/png' : 'image/jpeg';

            return 'data:'.$mime.';base64,'.base64_encode($bytes);
        }

        $url = $row['url'] ?? null;
        if (! is_string($url) || $url === '') {
            return null;
        }

        if (str_starts_with($url, 'data:')) {
            return $url;
        }

        $relative = parse_url($url, PHP_URL_PATH) ?: $url;

        // Catalog assets under /images/diagnosis/* — prefer PNG for DomPDF
        if (is_string($relative) && str_starts_with($relative, '/images/')) {
            $absolute = public_path(ltrim($relative, '/'));
            if (str_ends_with(strtolower($absolute), '.svg')) {
                $pngSibling = preg_replace('/\.svg$/i', '.png', $absolute);
                if (is_string($pngSibling) && is_file($pngSibling)) {
                    return 'data:image/png;base64,'.base64_encode((string) file_get_contents($pngSibling));
                }
            }
            if (is_file($absolute)) {
                $bytes = (string) file_get_contents($absolute);
                $mime = str_ends_with(strtolower($absolute), '.svg') ? 'image/svg+xml' : 'image/png';

                return 'data:'.$mime.';base64,'.base64_encode($bytes);
            }
        }

        if (str_starts_with($relative, '/storage/')) {
            $diskPath = substr($relative, strlen('/storage/'));
            if (Storage::disk('public')->exists($diskPath)) {
                $bytes = Storage::disk('public')->get($diskPath);
                $mime = str_ends_with(strtolower($diskPath), '.png') ? 'image/png' : 'image/jpeg';

                return 'data:'.$mime.';base64,'.base64_encode($bytes);
            }
        }

        return null;
    }
}

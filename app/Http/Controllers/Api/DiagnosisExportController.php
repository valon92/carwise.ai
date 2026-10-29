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
            }

            $generatedAt = $data['generated_at']
                ?? ($evidence['unlocked_at'] ?? null)
                ?? now()->toIso8601String();

            $illustrations = [];
            foreach (($data['result']['illustrations'] ?? []) as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $dataUri = $this->illustrationDataUri($row);
                if ($dataUri) {
                    $illustrations[] = [
                        'src' => $dataUri,
                        'caption' => $row['caption'] ?? ($data['result']['problem_title'] ?? 'Problem illustration'),
                    ];
                }
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

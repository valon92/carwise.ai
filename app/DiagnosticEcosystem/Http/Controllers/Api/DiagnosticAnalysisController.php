<?php

namespace App\DiagnosticEcosystem\Http\Controllers\Api;

use App\DiagnosticEcosystem\Models\DeDiagnosticScan;
use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use App\DiagnosticEcosystem\Services\DiagnosticAnalysisService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class DiagnosticAnalysisController extends Controller
{
    public function __construct(
        private readonly DiagnosticAnalysisService $diagnosticAnalysisService,
    ) {}

    public function analyze(Request $request, int $scanId): JsonResponse
    {
        $scan = DeDiagnosticScan::whereHas('vehicleProfile', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($scanId);

        $profile = $scan->vehicleProfile;

        try {
            $analysis = $this->diagnosticAnalysisService->analyzeScan($scan, $profile);

            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'AI diagnostic analysis completed.',
            ], 201);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 503);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, int $scanId): JsonResponse
    {
        $scan = DeDiagnosticScan::whereHas('vehicleProfile', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($scanId);

        $analysis = $this->diagnosticAnalysisService->latestForScan($scan);

        if (! $analysis) {
            return response()->json([
                'success' => false,
                'message' => 'No AI analysis found for this scan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $profile = DeVehicleProfile::where('user_id', $request->user()->id)->findOrFail($vehicleId);

        return response()->json([
            'success' => true,
            'data' => $this->diagnosticAnalysisService->listForVehicle($profile),
        ]);
    }
}

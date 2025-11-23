<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DiagnosisExportController extends Controller
{
    /**
     * Export diagnosis to PDF
     */
    public function exportToPDF(Request $request): \Illuminate\Http\Response
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'vehicle' => 'required|array',
                'vehicle.make' => 'required|string|max:100',
                'vehicle.model' => 'required|string|max:100',
                'vehicle.year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'vehicle.mileage' => 'nullable|integer|min:0',
                'vehicle.engine_type' => 'nullable|string|max:50',
                'vehicle.engine_size' => 'nullable|string|max:50',
                'symptoms' => 'required|array',
                'description' => 'required|string',
                'result' => 'required|array',
                'generated_at' => 'required|string'
            ]);

            // Generate PDF
            $pdf = Pdf::loadView('diagnosis.export-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'CarWise_Diagnosis_' . $data['vehicle']['make'] . '_' . $data['vehicle']['model'] . '_' . date('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage());
            \Log::error('PDF Export Data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage(),
                'debug' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
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
                'title' => 'required|string|max:255',
                'vehicle' => 'required|array',
                'symptoms' => 'required|array',
                'description' => 'required|string',
                'result' => 'required|array',
                'generated_at' => 'required|string'
            ]);

            $filename = 'CarWise_Diagnosis_' . $data['vehicle']['make'] . '_' . $data['vehicle']['model'] . '_' . date('Y-m-d') . '.json';
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating JSON: ' . $e->getMessage()
            ], 500);
        }
    }
}

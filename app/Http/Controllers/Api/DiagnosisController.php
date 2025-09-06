<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DiagnosisController extends Controller
{
    public function index(Request $request)
    {
        $diagnoses = $request->user()->diagnoses()->with('car')->latest()->get();
        
        return response()->json([
            'success' => true,
            'diagnoses' => $diagnoses
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'description' => 'nullable|string|max:1000',
            'media_file' => 'required|file|mimes:jpg,jpeg,png,mp4,avi,mov,mp3,wav|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify car belongs to user
        $car = $request->user()->cars()->findOrFail($request->car_id);

        // Handle file upload
        $file = $request->file('media_file');
        $mediaType = $this->getMediaType($file->getMimeType());
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('diagnosis_media', $fileName, 'public');

        // Create diagnosis record
        $diagnosis = Diagnosis::create([
            'car_id' => $request->car_id,
            'user_id' => $request->user()->id,
            'media_file' => $filePath,
            'media_type' => $mediaType,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // Simulate AI analysis (in real app, this would be a job/queue)
        $this->simulateAIAnalysis($diagnosis);

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis submitted successfully',
            'diagnosis' => $diagnosis->load('car')
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $diagnosis = $request->user()->diagnoses()->with('car')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'diagnosis' => $diagnosis
        ]);
    }

    private function getMediaType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }
        
        return 'unknown';
    }

    private function simulateAIAnalysis($diagnosis)
    {
        // Simulate AI analysis with mock data
        $mockAnalysis = [
            'problem' => 'Engine oil leak detected near the oil pan gasket',
            'confidence' => 85,
            'solutions' => [
                'Check oil pan gasket for damage or wear',
                'Replace oil pan gasket if necessary',
                'Check oil level and top up if needed',
                'Monitor for continued leaks after repair'
            ],
            'next_steps' => 'This appears to be a minor oil leak. We recommend having a mechanic inspect the oil pan gasket and replace it if necessary. The repair should be relatively inexpensive.'
        ];

        $diagnosis->update([
            'ai_analysis' => $mockAnalysis,
            'problem' => $mockAnalysis['problem'],
            'confidence' => $mockAnalysis['confidence'],
            'solutions' => $mockAnalysis['solutions'],
            'next_steps' => $mockAnalysis['next_steps'],
            'status' => 'completed',
        ]);
    }
}

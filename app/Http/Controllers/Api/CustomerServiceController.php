<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerServicePricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function index(CustomerServicePricing $pricing): JsonResponse
    {
        return response()->json([
            'success' => true,
            'services' => $pricing->publicPrices(),
        ]);
    }

    public function diagnosisQuote(Request $request, CustomerServicePricing $pricing): JsonResponse
    {
        $data = $request->validate([
            'photos' => 'nullable|integer|min:0|max:5',
            'videos' => 'nullable|integer|min:0|max:2',
            'audio' => 'nullable|boolean',
        ]);

        return response()->json([
            'success' => true,
            'amount' => $pricing->quoteDiagnosis(
                (int) ($data['photos'] ?? 0),
                (int) ($data['videos'] ?? 0),
                (bool) ($data['audio'] ?? false),
            ),
        ]);
    }
}

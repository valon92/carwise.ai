<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CarImageController extends Controller
{
    /**
     * Get primary image for a specific car
     */
    public function getPrimaryImage(Request $request): JsonResponse
    {
        $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string'
        ]);

        $image = CarImage::getPrimaryImage(
            $request->brand,
            $request->model,
            $request->year,
            $request->color
        );

        if (!$image) {
            // Try to get brand fallback
            $image = CarImage::getBrandFallbackImage($request->brand);
        }

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'No image found for this car',
                'image' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'image' => $image
        ]);
    }

    /**
     * Get all images for a specific car
     */
    public function getCarImages(Request $request): JsonResponse
    {
        $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
            'color' => 'nullable|string'
        ]);

        $images = CarImage::getCarImages(
            $request->brand,
            $request->model,
            $request->year,
            $request->color
        );

        return response()->json([
            'success' => true,
            'images' => $images
        ]);
    }

    /**
     * Get brand fallback image
     */
    public function getBrandFallback(Request $request): JsonResponse
    {
        $request->validate([
            'brand' => 'required|string'
        ]);

        $image = CarImage::getBrandFallbackImage($request->brand);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'No fallback image found for this brand',
                'image' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'image' => $image
        ]);
    }

    /**
     * Get default car image
     */
    public function getDefaultImage(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'image' => [
                'image_url' => '/images/cars/default-car.svg',
                'thumbnail_url' => '/images/cars/default-car.svg',
                'brand' => 'Default',
                'model' => 'Car',
                'color' => 'gray'
            ]
        ]);
    }
}
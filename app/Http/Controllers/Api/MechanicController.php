<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mechanic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MechanicController extends Controller
{
    public function index(Request $request)
    {
        $query = Mechanic::with('user');

        // Apply filters
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('expertise')) {
            $query->whereJsonContains('expertise', $request->expertise);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $mechanics = $query->where('is_verified', true)->get();

        return response()->json([
            'success' => true,
            'mechanics' => $mechanics
        ]);
    }

    public function show($id)
    {
        $mechanic = Mechanic::with('user')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'mechanic' => $mechanic
        ]);
    }

    public function update(Request $request, $id)
    {
        $mechanic = $request->user()->mechanic;
        
        if (!$mechanic || $mechanic->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'experience_years' => 'integer|min:0',
            'expertise' => 'array',
            'location' => 'string|max:255',
            'hourly_rate' => 'numeric|min:0',
            'bio' => 'nullable|string|max:1000',
            'availability' => 'in:available,busy,offline',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $mechanic->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'mechanic' => $mechanic->load('user')
        ]);
    }
}

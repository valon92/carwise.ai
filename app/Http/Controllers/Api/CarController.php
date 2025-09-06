<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $cars = $request->user()->cars()->with('diagnoses')->get();
        
        return response()->json([
            'success' => true,
            'cars' => $cars
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => 'nullable|string|max:17|unique:cars',
            'color' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $car = $request->user()->cars()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Car added successfully',
            'car' => $car
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $car = $request->user()->cars()->with('diagnoses')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'car' => $car
        ]);
    }

    public function update(Request $request, $id)
    {
        $car = $request->user()->cars()->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => 'nullable|string|max:17|unique:cars,vin,' . $car->id,
            'color' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $car->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Car updated successfully',
            'car' => $car
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $car = $request->user()->cars()->findOrFail($id);
        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car deleted successfully'
        ]);
    }
}

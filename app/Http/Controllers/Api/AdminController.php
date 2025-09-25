<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\DiagnosisSession;
use App\Models\DiagnosisResult;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return $next($request);
        });
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'customers' => User::where('role', 'customer')->count(),
                'mechanics' => User::where('role', 'mechanic')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'active' => User::where('status', 'active')->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            ],
            'cars' => [
                'total' => Car::count(),
                'active' => Car::where('status', 'active')->count(),
                'new_this_month' => Car::whereMonth('created_at', now()->month)->count(),
            ],
            'diagnoses' => [
                'total' => DiagnosisSession::count(),
                'completed' => DiagnosisSession::where('status', 'completed')->count(),
                'processing' => DiagnosisSession::where('status', 'processing')->count(),
                'new_this_month' => DiagnosisSession::whereMonth('created_at', now()->month)->count(),
            ],
            'car_brands' => [
                'total' => CarBrand::count(),
                'active' => CarBrand::where('is_active', true)->count(),
            ],
            'car_models' => [
                'total' => CarModel::count(),
                'active' => CarModel::where('is_active', true)->count(),
            ],
            'currencies' => [
                'total' => Currency::count(),
                'active' => Currency::where('is_active', true)->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get users with pagination and filters
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::with(['preferredCurrency']);

        // Apply filters
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $user = User::findOrFail($id);
        $user->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Get cars with pagination and filters
     */
    public function cars(Request $request): JsonResponse
    {
        $query = Car::with(['user', 'brand', 'model']);

        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $cars = $query->orderBy('created_at', 'desc')
                     ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $cars
        ]);
    }

    /**
     * Get diagnosis sessions with pagination and filters
     */
    public function diagnoses(Request $request): JsonResponse
    {
        $query = DiagnosisSession::with(['user', 'result']);

        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('severity') && $request->severity !== 'all') {
            $query->whereHas('result', function ($q) use ($request) {
                $q->where('severity', $request->severity);
            });
        }

        $diagnoses = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $diagnoses
        ]);
    }

    /**
     * Get car brands management
     */
    public function carBrands(Request $request): JsonResponse
    {
        $query = CarBrand::withCount('models');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $brands = $query->orderBy('name')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }

    /**
     * Update car brand status
     */
    public function updateCarBrandStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $brand = CarBrand::findOrFail($id);
        $brand->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Car brand status updated successfully',
            'data' => $brand
        ]);
    }

    /**
     * Get car models management
     */
    public function carModels(Request $request): JsonResponse
    {
        $query = CarModel::with(['brand']);

        if ($request->has('brand_id')) {
            $query->where('car_brand_id', $request->brand_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generation', 'like', "%{$search}%");
            });
        }

        $models = $query->orderBy('name')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $models
        ]);
    }

    /**
     * Update car model status
     */
    public function updateCarModelStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $model = CarModel::findOrFail($id);
        $model->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Car model status updated successfully',
            'data' => $model
        ]);
    }

    /**
     * Get currencies management
     */
    public function currencies(Request $request): JsonResponse
    {
        $query = Currency::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $currencies = $query->orderBy('sort_order')
                           ->orderBy('name')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * Update currency exchange rate
     */
    public function updateCurrencyRate(Request $request, $id): JsonResponse
    {
        $request->validate([
            'exchange_rate' => 'required|numeric|min:0'
        ]);

        $currency = Currency::findOrFail($id);
        $currency->update(['exchange_rate' => $request->exchange_rate]);

        return response()->json([
            'success' => true,
            'message' => 'Currency exchange rate updated successfully',
            'data' => $currency
        ]);
    }

    /**
     * Get system logs (recent activities)
     */
    public function systemLogs(Request $request): JsonResponse
    {
        // This would typically come from a logging system
        // For now, we'll return recent user activities
        $activities = [
            [
                'id' => 1,
                'type' => 'user_registration',
                'message' => 'New user registered: John Doe',
                'timestamp' => now()->subMinutes(5),
                'user_id' => 1
            ],
            [
                'id' => 2,
                'type' => 'diagnosis_completed',
                'message' => 'Diagnosis completed for BMW 3 Series',
                'timestamp' => now()->subMinutes(10),
                'user_id' => 2
            ],
            [
                'id' => 3,
                'type' => 'car_added',
                'message' => 'New car added: Porsche 911',
                'timestamp' => now()->subMinutes(15),
                'user_id' => 3
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mechanic;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'role' => 'required|in:customer,mechanic',
            'experience_years' => 'required_if:role,mechanic|integer|min:0|max:50',
            'expertise' => 'required_if:role,mechanic|array|min:1',
            'hourly_rate' => 'nullable|numeric|min:0|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'location' => $request->location,
            'bio' => $request->bio,
            'role' => $request->role,
            'status' => 'active',
            'timezone' => $request->timezone ?? 'UTC',
            'language' => $request->language ?? 'en',
            'notification_preferences' => [
                'email_notifications' => true,
                'push_notifications' => true,
                'sms_notifications' => false,
                'diagnosis_updates' => true,
                'marketing_emails' => false
            ],
            'privacy_settings' => [
                'profile_visibility' => 'private',
                'show_email' => false,
                'show_phone' => false,
                'allow_contact' => true
            ]
        ]);

        if ($request->role === 'mechanic') {
            Mechanic::create([
                'user_id' => $user->id,
                'experience_years' => $request->experience_years,
                'expertise' => $request->expertise,
                'location' => $request->location,
                'hourly_rate' => $request->hourly_rate ?? 25.00,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Log registration activity
        UserActivityService::log(
            'registration',
            'User registered successfully',
            $user->id,
            null,
            $request
        );

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update user login information
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'login_history' => array_merge(
                $user->login_history ?? [],
                [
                    [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'login_at' => now()->toISOString()
                    ]
                ]
            )
        ]);

        // Create user session
        UserActivityService::createSession($user->id, $request);

        // Log login activity
        UserActivityService::logLogin($user->id, $request);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Log logout activity
        UserActivityService::logLogout($user->id, $request);

        // Deactivate user session
        UserActivityService::updateSessionActivity(session()->getId());

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'location' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8|confirmed',
            'experience_years' => 'sometimes|integer|min:0|max:50',
            'hourly_rate' => 'sometimes|numeric|min:0',
            'bio' => 'sometimes|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only([
                'first_name', 'last_name', 'email', 'phone', 'location',
                'experience_years', 'hourly_rate', 'bio'
            ]);

            // Update name if first_name or last_name changed
            if ($request->has('first_name') || $request->has('last_name')) {
                $firstName = $request->get('first_name', $user->first_name);
                $lastName = $request->get('last_name', $user->last_name);
                $updateData['name'] = trim($firstName . ' ' . $lastName);
            }

            // Hash password if provided
            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Remove null values
            $updateData = array_filter($updateData, function($value) {
                return $value !== null && $value !== '';
            });

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

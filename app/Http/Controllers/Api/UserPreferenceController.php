<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    /**
     * Get all user preferences
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $preferences = UserPreference::where('user_id', $user->id)
            ->orderBy('preference_key')
            ->get()
            ->map(function ($preference) {
                return [
                    'id' => $preference->id,
                    'key' => $preference->preference_key,
                    'value' => $preference->value,
                    'type' => $preference->preference_type,
                    'description' => $preference->description,
                    'is_public' => $preference->is_public,
                    'updated_at' => $preference->updated_at
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $preferences
        ]);
    }

    /**
     * Get a specific preference
     */
    public function show(Request $request, $key)
    {
        $user = $request->user();
        
        $preference = UserPreference::where('user_id', $user->id)
            ->where('preference_key', $key)
            ->first();

        if (!$preference) {
            return response()->json([
                'success' => false,
                'message' => 'Preference not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $preference->id,
                'key' => $preference->preference_key,
                'value' => $preference->value,
                'type' => $preference->preference_type,
                'description' => $preference->description,
                'is_public' => $preference->is_public,
                'updated_at' => $preference->updated_at
            ]
        ]);
    }

    /**
     * Set a preference value
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'nullable|string|in:string,boolean,integer,float,json',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $key = $request->input('key');
        $value = $request->input('value');
        $type = $request->input('type', 'string');
        $description = $request->input('description');
        $isPublic = $request->input('is_public', false);

        try {
            $preference = UserPreference::set(
                $user->id,
                $key,
                $value,
                $type,
                $description
            );

            $preference->is_public = $isPublic;
            $preference->save();

            return response()->json([
                'success' => true,
                'message' => 'Preference saved successfully',
                'data' => [
                    'id' => $preference->id,
                    'key' => $preference->preference_key,
                    'value' => $preference->value,
                    'type' => $preference->preference_type,
                    'description' => $preference->description,
                    'is_public' => $preference->is_public,
                    'updated_at' => $preference->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save preference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a preference
     */
    public function update(Request $request, $key)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'type' => 'nullable|string|in:string,boolean,integer,float,json',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $preference = UserPreference::where('user_id', $user->id)
            ->where('preference_key', $key)
            ->first();

        if (!$preference) {
            return response()->json([
                'success' => false,
                'message' => 'Preference not found'
            ], 404);
        }

        try {
            $preference->preference_value = $request->input('value');
            $preference->preference_type = $request->input('type', $preference->preference_type);
            $preference->description = $request->input('description', $preference->description);
            $preference->is_public = $request->input('is_public', $preference->is_public);
            $preference->save();

            return response()->json([
                'success' => true,
                'message' => 'Preference updated successfully',
                'data' => [
                    'id' => $preference->id,
                    'key' => $preference->preference_key,
                    'value' => $preference->value,
                    'type' => $preference->preference_type,
                    'description' => $preference->description,
                    'is_public' => $preference->is_public,
                    'updated_at' => $preference->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update preference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a preference
     */
    public function destroy(Request $request, $key)
    {
        $user = $request->user();
        
        $preference = UserPreference::where('user_id', $user->id)
            ->where('preference_key', $key)
            ->first();

        if (!$preference) {
            return response()->json([
                'success' => false,
                'message' => 'Preference not found'
            ], 404);
        }

        try {
            $preference->delete();

            return response()->json([
                'success' => true,
                'message' => 'Preference deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete preference',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart-specific preferences
     */
    public function getCartPreferences(Request $request)
    {
        $user = $request->user();
        
        $cartPreferences = UserPreference::where('user_id', $user->id)
            ->where('preference_key', 'like', 'cart_%')
            ->get()
            ->mapWithKeys(function ($preference) {
                return [$preference->preference_key => $preference->value];
            });

        // Set default cart preferences if they don't exist
        $defaults = [
            'cart_auto_save' => true,
            'cart_notifications' => true,
            'cart_currency' => 'USD',
            'cart_shipping_preference' => 'standard',
            'cart_payment_preference' => 'credit_card',
            'cart_quantity_limit' => 10,
            'cart_show_tax' => true,
            'cart_show_shipping' => true,
            'cart_remember_address' => true,
            'cart_auto_apply_coupons' => false
        ];

        foreach ($defaults as $key => $defaultValue) {
            if (!$cartPreferences->has($key)) {
                UserPreference::set($user->id, $key, $defaultValue, 'boolean', "Cart preference: {$key}");
                $cartPreferences[$key] = $defaultValue;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $cartPreferences
        ]);
    }

    /**
     * Set cart-specific preferences
     */
    public function setCartPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'preferences' => 'required|array',
            'preferences.*' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $preferences = $request->input('preferences');

        try {
            foreach ($preferences as $key => $value) {
                if (str_starts_with($key, 'cart_')) {
                    $type = is_bool($value) ? 'boolean' : (is_numeric($value) ? 'float' : 'string');
                    UserPreference::set($user->id, $key, $value, $type, "Cart preference: {$key}");
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cart preferences saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save cart preferences',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}



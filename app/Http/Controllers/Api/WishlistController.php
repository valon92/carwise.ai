<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $priority = $request->get('priority');
        $category = $request->get('category');
        $search = $request->get('search');

        $query = Wishlist::where('user_id', $user->id)
            ->orderBy('priority', 'desc')
            ->orderBy('added_at', 'desc');

        // Filter by priority
        if ($priority) {
            $query->where('priority', $priority);
        }

        // Filter by category
        if ($category) {
            $query->where('part_category', $category);
        }

        // Search by part name or brand
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('part_name', 'like', '%' . $search . '%')
                  ->orWhere('part_brand', 'like', '%' . $search . '%')
                  ->orWhere('part_number', 'like', '%' . $search . '%');
            });
        }

        $wishlist = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $wishlist->items(),
            'pagination' => [
                'current_page' => $wishlist->currentPage(),
                'last_page' => $wishlist->lastPage(),
                'per_page' => $wishlist->perPage(),
                'total' => $wishlist->total(),
                'has_more' => $wishlist->hasMorePages()
            ]
        ]);
    }

    /**
     * Add item to wishlist
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'part_id' => 'nullable|string',
            'part_name' => 'required|string|max:255',
            'part_brand' => 'nullable|string|max:255',
            'part_number' => 'nullable|string|max:255',
            'part_description' => 'nullable|string',
            'part_image_url' => 'nullable|url',
            'part_category' => 'nullable|string|max:255',
            'part_price' => 'required|numeric|min:0',
            'part_currency' => 'nullable|string|size:3',
            'source' => 'nullable|string|max:255',
            'affiliate_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'notification_enabled' => 'nullable|boolean',
            'price_alert_threshold' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check if item already exists in wishlist
        $existingItem = Wishlist::where('user_id', $user->id)
            ->where('part_id', $request->input('part_id'))
            ->where('part_name', $request->input('part_name'))
            ->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item already exists in wishlist'
            ], 409);
        }

        try {
            $wishlistItem = Wishlist::create([
                'user_id' => $user->id,
                'part_id' => $request->input('part_id'),
                'part_name' => $request->input('part_name'),
                'part_brand' => $request->input('part_brand'),
                'part_number' => $request->input('part_number'),
                'part_description' => $request->input('part_description'),
                'part_image_url' => $request->input('part_image_url'),
                'part_category' => $request->input('part_category'),
                'part_price' => $request->input('part_price'),
                'part_currency' => $request->input('part_currency', 'USD'),
                'source' => $request->input('source', 'carwise'),
                'affiliate_url' => $request->input('affiliate_url'),
                'notes' => $request->input('notes'),
                'priority' => $request->input('priority', 'medium'),
                'notification_enabled' => $request->input('notification_enabled', false),
                'price_alert_threshold' => $request->input('price_alert_threshold'),
                'added_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to wishlist successfully',
                'data' => $wishlistItem
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update wishlist item
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'notification_enabled' => 'nullable|boolean',
            'price_alert_threshold' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found'
            ], 404);
        }

        try {
            $wishlistItem->update([
                'notes' => $request->input('notes', $wishlistItem->notes),
                'priority' => $request->input('priority', $wishlistItem->priority),
                'notification_enabled' => $request->input('notification_enabled', $wishlistItem->notification_enabled),
                'price_alert_threshold' => $request->input('price_alert_threshold', $wishlistItem->price_alert_threshold)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item updated successfully',
                'data' => $wishlistItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wishlist item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from wishlist
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found'
            ], 404);
        }

        try {
            $wishlistItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from wishlist successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move item from wishlist to cart
     */
    public function moveToCart(Request $request, $id)
    {
        $user = $request->user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found'
            ], 404);
        }

        try {
            // Return the item data for cart addition
            $cartItem = [
                'id' => $wishlistItem->part_id ?: 'wishlist_' . $wishlistItem->id,
                'name' => $wishlistItem->part_name,
                'brand' => $wishlistItem->part_brand,
                'part_number' => $wishlistItem->part_number,
                'price' => $wishlistItem->part_price,
                'image_url' => $wishlistItem->part_image_url,
                'category' => $wishlistItem->part_category,
                'description' => $wishlistItem->part_description,
                'source' => $wishlistItem->source,
                'affiliate_url' => $wishlistItem->affiliate_url,
                'formatted_price' => $wishlistItem->formatted_price
            ];

            return response()->json([
                'success' => true,
                'message' => 'Item ready to be added to cart',
                'data' => $cartItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare item for cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wishlist statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_items' => Wishlist::where('user_id', $user->id)->count(),
            'high_priority_items' => Wishlist::where('user_id', $user->id)->where('priority', 'high')->count(),
            'medium_priority_items' => Wishlist::where('user_id', $user->id)->where('priority', 'medium')->count(),
            'low_priority_items' => Wishlist::where('user_id', $user->id)->where('priority', 'low')->count(),
            'items_with_alerts' => Wishlist::where('user_id', $user->id)->where('notification_enabled', true)->count(),
            'recent_additions' => Wishlist::where('user_id', $user->id)->recent(7)->count(),
            'categories' => Wishlist::where('user_id', $user->id)
                ->selectRaw('part_category, COUNT(*) as count')
                ->groupBy('part_category')
                ->orderBy('count', 'desc')
                ->get()
                ->pluck('count', 'part_category')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Check if item is in wishlist
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'part_id' => 'nullable|string',
            'part_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('part_id', $request->input('part_id'))
            ->where('part_name', $request->input('part_name'))
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'in_wishlist' => !!$wishlistItem,
                'item' => $wishlistItem
            ]
        ]);
    }
}



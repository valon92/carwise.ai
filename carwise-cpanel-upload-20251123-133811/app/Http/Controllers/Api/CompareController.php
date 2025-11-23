<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compare;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CompareController extends Controller
{
    /**
     * Get user's compare list
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $source = $request->get('source');
        $category = $request->get('category');

        $query = Compare::where('user_id', $user->id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('added_at', 'desc');

        // Filter by source
        if ($source) {
            $query->bySource($source);
        }

        // Filter by category
        if ($category) {
            $query->byCategory($category);
        }

        $compareList = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $compareList->items(),
            'pagination' => [
                'current_page' => $compareList->currentPage(),
                'last_page' => $compareList->lastPage(),
                'per_page' => $compareList->perPage(),
                'total' => $compareList->total(),
                'has_more' => $compareList->hasMorePages()
            ]
        ]);
    }

    /**
     * Add item to compare list
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
            'specifications' => 'nullable|array',
            'features' => 'nullable|array',
            'compatibility' => 'nullable|array',
            'warranty' => 'nullable|string',
            'shipping_info' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check if item already exists in compare list
        $existingItem = Compare::where('user_id', $user->id)
            ->where('part_id', $request->input('part_id'))
            ->where('part_name', $request->input('part_name'))
            ->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item already exists in compare list'
            ], 409);
        }

        // Check if compare list is full (max 4 items)
        $currentCount = Compare::where('user_id', $user->id)->count();
        if ($currentCount >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'Compare list is full. Maximum 4 items allowed.'
            ], 400);
        }

        try {
            $compareItem = Compare::create([
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
                'specifications' => $request->input('specifications'),
                'features' => $request->input('features'),
                'compatibility' => $request->input('compatibility'),
                'warranty' => $request->input('warranty'),
                'shipping_info' => $request->input('shipping_info'),
                'sort_order' => $currentCount + 1,
                'added_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to compare list successfully',
                'data' => $compareItem
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to compare list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update compare item
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sort_order' => 'nullable|integer|min:1|max:4',
            'specifications' => 'nullable|array',
            'features' => 'nullable|array',
            'compatibility' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $compareItem = Compare::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$compareItem) {
            return response()->json([
                'success' => false,
                'message' => 'Compare item not found'
            ], 404);
        }

        try {
            $compareItem->update([
                'sort_order' => $request->input('sort_order', $compareItem->sort_order),
                'specifications' => $request->input('specifications', $compareItem->specifications),
                'features' => $request->input('features', $compareItem->features),
                'compatibility' => $request->input('compatibility', $compareItem->compatibility)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Compare item updated successfully',
                'data' => $compareItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update compare item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from compare list
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $compareItem = Compare::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$compareItem) {
            return response()->json([
                'success' => false,
                'message' => 'Compare item not found'
            ], 404);
        }

        try {
            $compareItem->delete();

            // Reorder remaining items
            $remainingItems = Compare::where('user_id', $user->id)
                ->orderBy('sort_order')
                ->get();

            foreach ($remainingItems as $index => $item) {
                $item->update(['sort_order' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item removed from compare list successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from compare list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all items from compare list
     */
    public function clear(Request $request)
    {
        $user = $request->user();

        try {
            Compare::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Compare list cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear compare list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if item is in compare list
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
        
        $compareItem = Compare::where('user_id', $user->id)
            ->where('part_id', $request->input('part_id'))
            ->where('part_name', $request->input('part_name'))
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'in_compare' => !!$compareItem,
                'item' => $compareItem
            ]
        ]);
    }

    /**
     * Get compare statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_items' => Compare::where('user_id', $user->id)->count(),
            'items_by_source' => Compare::where('user_id', $user->id)
                ->selectRaw('source, COUNT(*) as count')
                ->groupBy('source')
                ->get()
                ->pluck('count', 'source'),
            'items_by_category' => Compare::where('user_id', $user->id)
                ->selectRaw('part_category, COUNT(*) as count')
                ->groupBy('part_category')
                ->orderBy('count', 'desc')
                ->get()
                ->pluck('count', 'part_category'),
            'recent_additions' => Compare::where('user_id', $user->id)->recent(7)->count(),
            'average_price' => Compare::where('user_id', $user->id)->avg('part_price'),
            'price_range' => [
                'min' => Compare::where('user_id', $user->id)->min('part_price'),
                'max' => Compare::where('user_id', $user->id)->max('part_price')
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Reorder compare items
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.sort_order' => 'required|integer|min:1|max:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        try {
            foreach ($request->input('items') as $item) {
                Compare::where('user_id', $user->id)
                    ->where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Compare list reordered successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder compare list',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
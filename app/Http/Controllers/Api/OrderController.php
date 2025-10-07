<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get user's order history
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Order::where('user_id', $user->id)
            ->with(['items'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Search by order number
        if ($search) {
            $query->where('order_number', 'like', '%' . $search . '%');
        }

        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'has_more' => $orders->hasMorePages()
            ]
        ]);
    }

    /**
     * Get a specific order
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $order = Order::where('user_id', $user->id)
            ->where('id', $id)
            ->with(['items', 'payments'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Create a new order from cart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_info' => 'required|array',
            'customer_info.first_name' => 'required|string',
            'customer_info.last_name' => 'required|string',
            'customer_info.email' => 'required|email',
            'customer_info.phone' => 'required|string',
            'shipping_address' => 'required|array',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.zip_code' => 'required|string',
            'shipping_address.country' => 'required|string',
            'payment_method' => 'required|string',
            'currency' => 'nullable|string|size:3',
            'notes' => 'nullable|string'
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
            DB::beginTransaction();

            // Calculate totals
            $items = $request->input('items');
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'part_id' => $item['id'],
                    'part_name' => $item['name'],
                    'part_brand' => $item['brand'] ?? null,
                    'part_number' => $item['part_number'] ?? null,
                    'part_description' => $item['description'] ?? null,
                    'part_image_url' => $item['image_url'] ?? null,
                    'part_category' => $item['category'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $itemTotal,
                    'source' => $item['source'] ?? 'carwise',
                    'affiliate_url' => $item['affiliate_url'] ?? null,
                    'tracking_data' => $item['tracking_data'] ?? null
                ];
            }

            // Calculate shipping and tax
            $shippingAmount = $subtotal > 100 ? 0 : 9.99; // Free shipping over $100
            $taxAmount = $subtotal * 0.08; // 8% tax
            $totalAmount = $subtotal + $shippingAmount + $taxAmount;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'currency' => $request->input('currency', 'USD'),
                'payment_method' => $request->input('payment_method'),
                'payment_status' => 'pending',
                'shipping_address' => $request->input('shipping_address'),
                'billing_address' => $request->input('billing_address', $request->input('shipping_address')),
                'customer_info' => $request->input('customer_info'),
                'notes' => $request->input('notes')
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }

            // Create payment record
            Payment::create([
                'user_id' => $user->id,
                'payment_id' => 'PAY-' . $order->order_number,
                'provider' => $request->input('payment_method'),
                'type' => 'order_payment',
                'status' => 'pending',
                'amount' => $totalAmount,
                'currency' => $request->input('currency', 'USD'),
                'description' => 'Payment for order ' . $order->order_number,
                'related_type' => 'order',
                'related_id' => $order->id,
                'payment_method' => $request->input('payment_method')
            ]);

            DB::commit();

            // Load the order with items
            $order->load('items');

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status (admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::findOrFail($id);
        $status = $request->input('status');

        try {
            $order->status = $status;
            
            // Set timestamps based on status
            switch ($status) {
                case 'shipped':
                    $order->shipped_at = now();
                    if ($request->has('tracking_number')) {
                        $order->tracking_number = $request->input('tracking_number');
                    }
                    break;
                case 'delivered':
                    $order->delivered_at = now();
                    break;
                case 'cancelled':
                    $order->cancelled_at = now();
                    if ($request->has('notes')) {
                        $order->cancellation_reason = $request->input('notes');
                    }
                    break;
                case 'refunded':
                    $order->refunded_at = now();
                    if ($request->has('notes')) {
                        $order->refund_reason = $request->input('notes');
                    }
                    break;
            }

            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        
        $order = Order::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        try {
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->cancellation_reason = $request->input('reason', 'Cancelled by customer');
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics for user
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_spent' => Order::where('user_id', $user->id)->where('status', '!=', 'cancelled')->sum('total_amount'),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'shipped_orders' => Order::where('user_id', $user->id)->where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            'recent_orders' => Order::where('user_id', $user->id)->recent(30)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}



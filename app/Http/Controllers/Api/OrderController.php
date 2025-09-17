<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Admin can see all orders, users can only see their own
        if ($request->user()->isAdmin()) {
            $orders = Order::with(['user', 'orderItems.product'])->get();
        } else {
            $orders = Order::with(['orderItems.product'])
                ->where('user_id', $request->user()->id)
                ->get();
        }

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $orderItems = [];

            // Validate stock availability and calculate total
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if (!$product->hasSufficientStock($item['quantity'])) {
                    return response()->json([
                        'message' => "Insufficient stock for product: {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}"
                    ], Response::HTTP_BAD_REQUEST);
                }

                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'product' => $product
                ];
            }

            // Create the order
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Create order items and reduce stock
            foreach ($orderItems as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Reduce product stock
                $item['product']->reduceStock($item['quantity']);
            }

            $order->load(['orderItems.product', 'user']);

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ], Response::HTTP_CREATED);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Order $order)
    {
        // Ensure users can only see their own orders (unless admin)
        if (!$request->user()->isAdmin() && $order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Access denied. You can only view your own orders.'
            ], Response::HTTP_FORBIDDEN);
        }

        $order->load(['orderItems.product', 'user']);

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Only admin can update order status
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Access denied. Only admins can update orders.'
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Order $order)
    {
        // Only admin can delete orders
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Access denied. Only admins can delete orders.'
            ], Response::HTTP_FORBIDDEN);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}

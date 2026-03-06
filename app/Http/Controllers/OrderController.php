<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Models\Modifier;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\OrderItemModifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $status = request('status');

        $orders = Order::with('table')
            ->filter(request())
            ->whereDate('created_at', now()->toDateString())
            ->get();
        // dd($orders);


        $tables = Table::whereNull('deleted_at')->get();

        return view('orders.index', compact('orders', 'status', 'tables'));
    }

    /**
     * API endpoint for fetching orders data as JSON (used for auto-refresh)
     */
    public function fetchOrders()
    {
        $status = request('status');

        $orders = Order::with('table')
            ->filter(request())
            ->whereDate('created_at', now()->toDateString())
            ->get();

        $tables = Table::whereNull('deleted_at')->get();

        return response()->json([
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                    'table' => $order->table ? $order->table->slug : ($order->table_name ?? 'N/A'),
                    'order_type' => ucfirst($order->order_type),
                    'total_price' => number_format($order->total_price, 2),
                    'total_qty' => $order->total_qty,
                    'status' => $order->status,
                    'payment_type' => $order->payment_type,
                    'payment_image_path' => $order->payment_image_path,
                    'is_paid' => $order->isPaid(),
                    'is_payment_cash' => $order->isPaymentCash(),
'is_payment_online' => $order->isPaymentPromptPay(),
                    'is_payment_verified' => $order->isPaymentVerified(),
                    'is_unpaid' => $order->isUnpaid(),
                    'urls' => [
                        'show' => route('orders.show', $order),
                        'update_status' => route('orders.update-status', $order),
                        'update_payment_type' => route('orders.update-payment-type', $order),
                        'update_payment_verification' => route('orders.update-payment-verification', $order),
                        'payment_image' => $order->payment_image_path ? route('orders.payment-image', $order) : null,
                    ]
                ];
            }),
            'tables' => $tables->map(function ($table) {
                return [
                    'id' => $table->id,
                    'slug' => $table->slug,
                ];
            }),
            'current_status' => $status,
        ]);
    }

    public function history()
    {
        $orders = Order::with('table')
            ->filterHistory(request())
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->orderby('created_at', 'desc')
            ->paginate(20);
        // dd($orders);

        return view('orders.history', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.menu', 'orderItems.orderItemModifiers.modifier']);

        return view('orders.order-items', compact('order'));
    }

    public function showHistoryOrder(Order $order)
    {
        $order->load(['orderItems.menu', 'orderItems.orderItemModifiers.modifier']);

        return view('orders.history-order-items', compact('order'));
    }

    public function updateOrderItem(Request $request, Order $order, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'protein_id' => 'nullable|integer|exists:modifiers,id',
            'flavor_id' => 'nullable|integer|exists:modifiers,id',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'integer|exists:modifiers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $orderItem = OrderItem::findOrFail($itemId);

            // Ensure the order item belongs to the specified order
            if ($orderItem->order->order_code !== $order->order_code) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $menu = $orderItem->menu;

            // dd($request->quantity);

            // Update quantity
            $orderItem->quantity = $request->quantity;

            // Calculate base price in order item
            // price can change overtime, so take record for the price.
            $itemPrice = $menu->price;
            $itemTotalPrice = $orderItem->price * $request->quantity;
            // dd($orderItem->menu->eng_name, $orderItem->price, $itemTotalPrice);

            // Remove existing modifiers
            $orderItem->orderItemModifiers()->delete();

            // Add new modifiers
            $modifierPrice = 0;

            if ($request->protein_id) {
                $proteinModifier = Modifier::find($request->protein_id);
                OrderItemModifier::create([
                    'order_item_id' => $orderItem->id,
                    'modifier_id' => $proteinModifier->id,
                    'eng_name' => $proteinModifier->eng_name,
                    'mm_name' => $proteinModifier->mm_name,
                    'price' => $proteinModifier->price,
                ]);
                $modifierPrice += $proteinModifier->price;
            }

            if ($request->flavor_id) {
                $flavorModifier = Modifier::find($request->flavor_id);
                OrderItemModifier::create([
                    'order_item_id' => $orderItem->id,
                    'modifier_id' => $flavorModifier->id,
                    'eng_name' => $flavorModifier->eng_name,
                    'mm_name' => $flavorModifier->mm_name,
                    'price' => $flavorModifier->price,
                ]);
                $modifierPrice += $flavorModifier->price;
            }

            if (!empty($request->addon_ids)) {
                foreach ($request->addon_ids as $addonId) {
                    $addonModifier = Modifier::find($addonId);
                    OrderItemModifier::create([
                        'order_item_id' => $orderItem->id,
                        'modifier_id' => $addonModifier->id,
                        'eng_name' => $addonModifier->eng_name,
                        'mm_name' => $addonModifier->mm_name,
                        'price' => $addonModifier->price,
                    ]);
                    $modifierPrice += $addonModifier->price;
                }
            }
            $itemTotalPrice += $modifierPrice * $request->quantity;

            $orderItem->total_price = $itemTotalPrice;
            $orderItem->save();

            // Recalculate order totals
            $order->total_price = $order->orderItems->sum('total_price');
            $order->total_qty = $order->orderItems->sum('quantity');
            $order->save();

            DB::commit();

            return response()->json([
                'item_total' => number_format($itemTotalPrice, 2),
                'order_total' => number_format($order->total_price, 2),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteOrderItem(Order $order, $itemId)
    {
        DB::beginTransaction();
        try {
            $orderItem = OrderItem::findOrFail($itemId);

            // Ensure the order item belongs to the specified order
            if ($orderItem->order->order_code !== $order->order_code) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Delete the order item and its modifiers
            $orderItem->orderItemModifiers()->delete();
            $orderItem->delete();

            // Recalculate order totals
            $order->total_price = $order->orderItems->sum('total_price');
            $order->total_qty = $order->orderItems->sum('quantity');
            $order->save();

            DB::commit();

            return response()->json([
                'order_total' => number_format($order->total_price, 2),
                'message' => 'Order item deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,preparing,delivered,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function updatePaymentVerification(Request $request, Order $order)
    {
        // dd(gettype($request->payment_verified));
        $validator = Validator::make($request->all(), [
            'payment_verified' => 'required|in:true,false',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment_verified = $request->payment_verified === 'true' ? true : false;

        if ($payment_verified) {
            $order->update([
                'status' => 'completed',
                'payment_verified_at' => now(),
                'payment_verified_by' => auth()->id(),
            ]);
        } else {
            $order->update([
                'payment_verified_at' => null,
                'payment_verified_by' => null,
            ]);
        }

        return response()->json(['message' => 'Payment verification updated successfully.'], 200);
    }

    public function updatePaymentType(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
'payment_type' => 'nullable|in:cash,prompt_pay',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $paymentType = $request->payment_type ?: null;

        // If switching to cash or clearing payment type, remove any uploaded payment image
        if ($paymentType === 'cash') {
            if ($order->payment_image_path) {
                $path = storage_path('app/' . $order->payment_image_path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }

            $order->update([
                'payment_type' => $paymentType,
                'payment_status' => true,
                'payment_image_path' => null,
            ]);
        } elseif ($paymentType === null) {
            // If clearing payment type, also clear payment image and verification
            if ($order->payment_image_path) {
                $path = storage_path('app/' . $order->payment_image_path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }

            $order->update([
                'payment_type' => null,
                'payment_image_path' => null,
                'payment_verified_at' => null,
                'payment_verified_by' => null,
                'payment_status' => false,
            ]);
        } elseif ($paymentType === 'prompt_pay') {
            // For other payment types just update the type
            $order->update([
                'payment_type' => $paymentType,
            ]);
        }

        $order->refresh();

        return response()->json([
            'message' => 'Payment type updated successfully.',
            'payment_image_path' => $order->payment_image_path,
        ], 200);
    }

    public function showPaymentImage(Order $order)
    {
        if (!$order->payment_image_path) {
            abort(404, 'Payment image not found.');
        }

        $path = storage_path('app/' . $order->payment_image_path);

        if (!file_exists($path)) {
            abort(404, 'Payment image not found.');
        }

        return response()->file($path);
    }
}

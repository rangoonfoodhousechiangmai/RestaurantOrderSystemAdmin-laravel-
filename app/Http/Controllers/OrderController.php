<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.menu', 'orderItems.orderItemModifiers.modifier']);

        return view('orders.order-items', compact('order'));
    }

    public function updateOrderItem(Request $request, $itemId)
    {
        // dd($request->all(), $itemId);
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
            $order = $orderItem->order;
            $menu = $orderItem->menu;

            // Update quantity
            $orderItem->quantity = $request->quantity;

            // Calculate base price
            $itemPrice = $menu->price;
            $itemTotalPrice = $itemPrice * $request->quantity;

            // Remove existing modifiers
            $orderItem->orderItemModifiers()->delete();

            // Add new modifiers
            $modifierPrice = 0;

            if ($request->protein_id) {
                $proteinModifier = Modifier::find($request->protein_id);
                OrderItemModifier::create([
                    'order_item_id' => $orderItem->id,
                    'modifier_id' => $proteinModifier->id,
                    'name' => $proteinModifier->name,
                    'price' => $proteinModifier->price,
                ]);
                $modifierPrice += $proteinModifier->price;
            }

            if ($request->flavor_id) {
                $flavorModifier = Modifier::find($request->flavor_id);
                OrderItemModifier::create([
                    'order_item_id' => $orderItem->id,
                    'modifier_id' => $flavorModifier->id,
                    'name' => $flavorModifier->name,
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
                        'name' => $addonModifier->name,
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
}

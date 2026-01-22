<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Modifier;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemModifier;
use App\Models\Table;
use App\Models\TableSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class OrderController extends Controller
{

    public function getOrderHistory(Request $request)
    {
        $tableSession = $this->validateTableSession($request->tableSessionToken);

        $orders = Order::when($tableSession, function ($q) use ($tableSession) {
            $q->where('table_session_token', $tableSession->session_token);
        })
            ->with('table:id,slug')
            ->select(
                'id',
                'table_id', // REQUIRED for with()
                'order_code',
                'order_type',
                'total_price',
                'total_qty',
                'status',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();



        // $orders = Order::when($tableSession, function ($q) use ($tableSession) {
        //     $q->where('table_session_token', $tableSession->session_token);
        // })
        //     ->leftJoin('tables', 'tables.id', '=', 'orders.table_id')
        //     ->select('orders.id', 'table_id', 'tables.slug as table_slug', 'order_code', 'order_type', 'total_price', 'total_qty', 'status', 'orders.created_at')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        return response()->json(['orders' => $orders], 200);
        // return response()->json(['orderToken' => $request->orders, 'tableSessionToken' => $request->tableSessionToken], 200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_session_token' => 'nullable|string',
            'order_type' => 'required|in:dine_in,take_away',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.protein_id' => 'nullable|integer',
            'items.*.flavor_id' => 'nullable|integer',
            'items.*.addon_ids' => 'nullable|array',
            'items.*.addon_ids.*' => 'integer|exists:modifiers,id',
            'items.*.special_request' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {

            $tableSession = $this->validateTableSession($request->table_session_token);
            $orderCode = Helper::generateOrderCode();
            $orderToken = Helper::generateOrderToken();

            // creating order
            $order = Order::create([
                'order_code' => $orderCode,
                'order_token' => $orderToken,
                'table_id' => $tableSession->table_id,
                'table_session_token' => $tableSession->session_token,
                'order_type' => $request->order_type,
                'total_price' => 0,
                'total_qty' => 0,
                'status' => 'pending',
            ]);

            $totalPrice = 0;
            $totalQty = 0;

            // creating order items
            foreach ($request->items as $itemData) {
                $menu = Menu::find($itemData['menu_id']);
                if (!$menu->is_available) {
                    return response()->json(['error' => $menu->eng_name . ' is not available'], 422);
                }
                $itemPrice = $menu->price;
                $itemTotalPrice = $itemPrice * $itemData['quantity'];

                // Check if menu has protein or flavor modifiers
                $menuModifiers = $menu->modifiers;
                $hasProtein = $menuModifiers->where('type', 'protein')->isNotEmpty();
                $hasFlavor = $menuModifiers->where('type', 'flavor')->isNotEmpty();

                if ($hasProtein && !$itemData['protein_id']) {
                    throw new \Exception('Protein selection is required for this menu item.');
                }
                if ($hasFlavor && !$itemData['flavor_id']) {
                    throw new \Exception('Flavor selection is required for this menu item.');
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $itemData['menu_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemPrice,
                    'total_price' => $itemTotalPrice,
                    'special_request' => $itemData['special_request'] ?? null,
                ]);

                $totalPrice += $itemTotalPrice;
                $totalQty += $itemData['quantity'];

                if (!empty($itemData['addon_ids'])) {
                    foreach ($itemData['addon_ids'] as $addonId) {
                        $modifier = Modifier::find($addonId);
                        $modifierPrice = $modifier->price;

                        OrderItemModifier::create([
                            'order_item_id' => $orderItem->id,
                            'modifier_id' => $addonId,
                            'name' => $modifier->name,
                            'price' => $modifierPrice,
                        ]);

                        // update price for each item with modifier price
                        $itemTotalPrice += $modifierPrice * $itemData['quantity'];
                        $totalPrice += $modifierPrice * $itemData['quantity'];
                    }

                    // Update order item total price if modifiers added
                    $orderItem->update(['total_price' => $itemTotalPrice]);
                }

                $proteinPrice = 0;
                $flavorPrice = 0;

                if ($itemData['protein_id']) {
                    $proteinModifier = Modifier::find($itemData['protein_id']);
                    if ($proteinModifier) {
                        OrderItemModifier::create([
                            'order_item_id' => $orderItem->id,
                            'modifier_id' => $proteinModifier->id,
                            'name' => $proteinModifier->name,
                            'price' => $proteinModifier->price,
                        ]);
                        $proteinPrice = $proteinModifier->price;
                    }
                }

                if ($itemData['flavor_id']) {
                    $flavorModifier = Modifier::find($itemData['flavor_id']);
                    if ($flavorModifier) {
                        OrderItemModifier::create([
                            'order_item_id' => $orderItem->id,
                            'modifier_id' => $flavorModifier->id,
                            'name' => $flavorModifier->name,
                            'price' => $flavorModifier->price,
                        ]);
                        $flavorPrice = $flavorModifier->price;
                    }
                }

                $itemTotalPrice += ($proteinPrice + $flavorPrice) * $itemData['quantity'];
                $totalPrice += ($proteinPrice + $flavorPrice) * $itemData['quantity'];

                // Update order item total price after adding protein and flavor
                $orderItem->update(['total_price' => $itemTotalPrice]);
            }

            $order->update([
                'total_price' => $totalPrice,
                'total_qty' => $totalQty,
            ]);

            DB::commit();

            return response()->json(['message' => 'success'], 201);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    private function validateTableSession($session_token)
    {
        $tableSession = TableSession::where('session_token', $session_token)
            ->where('expires_at', '>', now())
            ->first();
        logger('tableSession', ['tableSession' => $tableSession]);
        if (!$tableSession) {
            throw new \Exception('Scan the Qr code plz.');
        }

        return $tableSession;
    }
}

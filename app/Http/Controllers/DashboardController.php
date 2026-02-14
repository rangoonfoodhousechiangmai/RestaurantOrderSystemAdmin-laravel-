<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IcehouseVentures\LaravelChartjs\Facades\Chartjs;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate the start and end of last week
        // $startOfCurrentWeek = Carbon::now()->startOfWeek();
        // $endOfCurrentWeek   = Carbon::now()->endOfWeek();
        $startOfCurrentWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfCurrentWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        // $lastWeek = Carbon::now()->subWeek();
        // $startOfLastWeek = $lastWeek->copy()->startOfWeek(Carbon::MONDAY);
        // $endOfLastWeek   = $lastWeek->copy()->endOfWeek(Carbon::SUNDAY);

        // Fetch top 10 best-selling items from last week
        $orderItems = OrderItem::select(
            'menus.id as menu_id',
            'menus.eng_name',
            'menus.mm_name',
            DB::raw('SUM(order_items.quantity) as total_sold_quantity')
        )
            ->leftjoin('menus', 'menus.id', '=', 'order_items.menu_id')
            ->leftjoin('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereNotNull('orders.payment_verified_at')
            ->whereBetween('order_items.created_at', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->groupBy('menus.id', 'menus.eng_name', 'menus.mm_name')
            ->orderByDesc('total_sold_quantity')
            ->limit(10)
            ->get();

        $todayOrderItemCount = OrderItem::whereDate('created_at', Carbon::today())
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed')
                    ->whereNotNull('payment_verified_at');
            })
            ->sum('quantity');

        $todayTotalRevenue = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->sum('total_price');

        // Fetch daily order counts for the current week
        $dailyOrderCounts = Order::whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])
            ->where('status', 'completed')
            ->whereNotNull('payment_verified_at')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Prepare data for the chart: days of the week up to today
        $labels = [];
        $data   = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfCurrentWeek->copy()->addDays($i);


            $labels[] = $date->format('l'); // Monday, Tuesday, ...
            $data[]   = $dailyOrderCounts[$date->toDateString()] ?? 0;
        }

        // Create bar chart
        $chart = Chartjs::build()
            ->name('weeklyOrders')
            ->type('bar')
            ->size(['width' => 400, 'height' => 200])
            ->labels($labels)
            ->datasets([
                [
                    'label' => 'Completed Orders',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 23, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ]
            ])
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'precision' => 0, // no decimals
                        ],
                    ],
                ],
            ]);
        return view('dashboard.index', compact('orderItems', 'todayOrderItemCount', 'todayTotalRevenue', 'chart'));
    }

    public function getData($period)
    {
        $orderItems = $this->getOrderItemsByPeriod($period);

        return response()->json(['orderItems' => $orderItems]);
    }

    private function getOrderItemsByPeriod($period)
    {
        if ($period === 'weekly') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($period === 'monthly') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } else {
            // Default to weekly
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        return OrderItem::select(
            'menus.id as menu_id',
            'menus.mm_name',
            DB::raw('SUM(order_items.quantity) as total_sold_quantity')
        )
            ->join('menus', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereNotNull('orders.payment_verified_at')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->groupBy('menus.id', 'menus.mm_name')
            ->orderByDesc('total_sold_quantity')
            ->limit(10)
            ->get();
    }
}

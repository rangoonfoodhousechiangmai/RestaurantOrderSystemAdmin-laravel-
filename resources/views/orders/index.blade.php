@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Orders</h4>
            </div>
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ !$status ? 'active' : '' }}" href="{{ route('orders.index') }}">All</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'pending']) }}">Pending</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $status == 'preparing' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'preparing']) }}">Preparing</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $status == 'delivered' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'delivered']) }}">Delivered</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $status == 'completed' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'completed']) }}">Completed</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $status == 'cancelled' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'cancelled']) }}">Cancelled</a>
                    </li>
                </ul>

                <!-- Table -->
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order Code</th>
                                        <th>Table</th>
                                        <th>Order Type</th>
                                        <th>Total Price</th>
                                        <th>Total Qty</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td><a href="{{ route('orders.show', $order) }}">{{ $order->order_code }}</a></td>
                                        <td>{{ $order->table->slug ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($order->order_type) }}</td>
                                        <td>{{ number_format($order->total_price, 2)}} THB</td>
                                        <td>{{ $order->total_qty }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'preparing' ? 'info' : ($order->status == 'delivered' ? 'primary' : ($order->status == 'completed' ? 'success' : 'danger'))) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No orders found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

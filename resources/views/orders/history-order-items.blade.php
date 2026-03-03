@extends('layouts.app', ['elementActive' => 'order-history'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Details - {{ $order->order_code }}</h4>
                    <a href="{{ route('orders.history') }}" class="btn btn-secondary">Back to Order History</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 col-sm-6 col-md-3 mb-2">
                            <strong>Table:</strong> {{ $order->table->slug ?? 'N/A' }}
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 mb-2">
                            <strong>Order Type:</strong> {{ ucfirst($order->order_type) }}
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 mb-2">
                            <strong>Status:</strong>
                            <span
                                class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'preparing' ? 'info' : ($order->status == 'delivered' ? 'primary' : ($order->status == 'completed' ? 'success' : 'danger'))) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 mb-2">
                            <strong>Total Price:</strong> <span
                                id="total-price">{{ number_format($order->total_price, 2) }}</span> THB
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" style="background-color: #f8f9fa;">
                            <thead>
                                <tr>
                                    <th>Menu Item</th>
                                    <th>Quantity</th>
                                    <th>Modifiers</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>SpecialRequest</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->orderItems as $item)
                                    <tr data-item-id="{{ $item->id }}">
                                        <td>{{ $item->menu->eng_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            @if($item->orderItemModifiers->isNotEmpty())
                                                <ul class="mb-0 ps-3">
                                                    @foreach($item->orderItemModifiers as $modifier)
                                                        <li>
                                                            {{ $modifier->eng_name }} / {{ $modifier->mm_name }}
                                                            (+{{ number_format($modifier->price, 2) }} THB)
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->price, 2) }} THB</td>
                                        <td class="item-total">{{ number_format($item->total_price, 2) }} THB</td>
                                        <td>{{ $item->special_request ?? '-' }}</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No order items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


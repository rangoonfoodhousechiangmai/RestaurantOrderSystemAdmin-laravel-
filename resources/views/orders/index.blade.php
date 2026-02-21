@extends('layouts.app', ['elementActive' => 'orders'])

@push('css')
    <style>
        /* Force table to be visible */
        .table-responsive {
            overflow-x: scroll !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        /* Make sure table has a minimum width */
        .table {
            min-width: 1500px;
            /* Adjust based on your column count */
            width: 100%;
        }

        /* Fix for Safari */
        @supports (-webkit-touch-callout: none) {
            .table-responsive {
                overflow-x: scroll !important;
            }
        }
    </style>
@endpush
@section('content')
    <div class="card">
        <div class="card-header my-3">
            <h2 class="text-center">Orders</h2>
        </div>
        @include('filters.order-filter')
        {{-- <div class="card-body"> --}}
        <!-- Tabs -->
        <ul class="nav nav-tabs mt-2" id="orderTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ !$status ? 'active' : '' }}"
                    href="?status=&order_code={{ request('order_code') }}&table_slug={{ request('table_slug') }}&order_type={{ request('order_type') }}&order_sort={{ request('order_sort') }}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                    href="?status=pending&order_code={{ request('order_code') }}&table_slug={{ request('table_slug') }}&order_type={{ request('order_type') }}&order_sort={{ request('order_sort') }}">
                    Pending
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'preparing' ? 'active' : '' }}"
                    href="?status=preparing&order_code={{ request('order_code') }}&table_slug={{ request('table_slug') }}&order_type={{ request('order_type') }}&order_sort={{ request('order_sort') }}">Preparing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'delivered' ? 'active' : '' }}"
                    href="?status=delivered&order_code={{ request('order_code') }}&table_slug={{ request('table_slug') }}&order_type={{ request('order_type') }}&order_sort={{ request('order_sort') }}">Delivered</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}"
                    href="?status=completed&order_code={{ request('order_code') }}&table_slug={{ request('table_slug') }}&order_type={{ request('order_type') }}&order_sort={{ request('order_sort') }}">Completed</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}"
                    href="?status=cancelled&order_code={{ request('order_code') }}&table_slug={{ request('table_slug') }}&order_type={{ request('order_type') }}&order_sort={{ request('order_sort') }}">Cancelled</a>
            </li>
        </ul>

        <!-- Table -->

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark" style="top: 0;">
                    <tr>
                        <th>OrderCode</th>
                        <th>OrderTime</th>
                        <th>Table</th>
                        <th>OrderType</th>
                        <th>TotalPrice</th>
                        <th>TotalQty</th>
                        <th>OrderStatus</th>
                        <th>PaymentType</th>
                        <th>PaymentImage</th>
                        <th>PaymentVerify</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}">{{ $order->order_code }}</a></td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $order->table->slug ?? ($order->table_name ?? 'N/A') }}</td>
                            <td>{{ ucfirst($order->order_type) }}</td>
                            <td>{{ number_format($order->total_price, 2) }} THB</td>
                            <td>{{ $order->total_qty }}</td>
                            <td>
                                <select class="form-select status-select" data-order-id="{{ $order->id }}"
                                    data-url="{{ route('orders.update-status', $order) }}" style="width:auto;">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>
                                        Preparing
                                    </option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                        Delivered
                                    </option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                        Completed
                                    </option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled
                                    </option>
                                </select>
                            </td>
                            <td>
                                {{-- @if ($order->isUnpaid()) --}}
                                <select class="form-select payment-type-select" data-order-id="{{ $order->id }}"
                                    data-url="{{ route('orders.update-payment-type', $order) }}" style="width:auto;">
                                    <option value="" {{ $order->isUnpaid() ? 'selected' : '' }}>Unpaid</option>
                                    <option value="cash" {{ $order->isPaymentCash() ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="online" {{ $order->isPaymentOnline() ? 'selected' : '' }}>Online
                                    </option>
                                </select>
                                {{-- @else
                                    -
                                @endif --}}
                            </td>
                            <td>
                                @if ($order->payment_image_path)
                                    <a href="{{ route('orders.payment-image', $order) }}" class="text-decoration-underline"
                                        target="_blank">Check
                                        Payment</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($order->isPaid() || $order->isPaymentOnline() || $order->isPaymentCash())
                                    <select class="form-select payment-verification-select"
                                        data-order-id="{{ $order->id }}" style="width:auto;">
                                        <option value="false" {{ !$order->isPaymentVerified() ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="true" {{ $order->isPaymentVerified() ? 'selected' : '' }}>Verified
                                        </option>
                                    </select>
                                @else
                                    -
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- </div> --}}
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {

            $('.payment-type-select').on('change', function() {
                var orderId = $(this).data('order-id');
                var paymentType = $(this).val();
                var selectElement = $(this);
                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        payment_type: paymentType,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        var row = selectElement.closest('tr');
                        var paymentImageCell = row.find('td').eq(8); // payment image column

                        if (!response.payment_image_path) {
                            paymentImageCell.html('-');
                        } else {
                            var anchor = '<a href="/orders/' + orderId +
                                '/payment-image" class="text-decoration-underline" target="_blank">Check Payment</a>';
                            paymentImageCell.html(anchor);
                        }
                        window.location.reload();

                    },
                    error: function(xhr) {
                        toastr.error('Failed to update payment type.');
                        selectElement.val(selectElement.data('original-value'));
                    }
                });
            });

            $('.status-select').on('change', function() {
                var orderId = $(this).data('order-id');
                var status = $(this).val();

                var selectElement = $(this);
                var url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);

                    },
                    error: function(xhr) {
                        toastr.error('Failed to update order status.');
                        // Revert the select to original value on error
                        selectElement.val(selectElement.data('original-value'));
                    }
                });
            });

            $('.payment-verification-select').on('change', function() {
                var orderId = $(this).data('order-id');
                var paymentVerified = $(this).val();
                var selectElement = $(this);

                $.ajax({
                    url: '/orders/' + orderId + '/update-payment-verification',
                    type: 'POST',
                    data: {
                        payment_verified: paymentVerified,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        // window.location.reload();
                    },
                    error: function(xhr) {
                        toastr.error('Failed to update payment verification.');
                        // Revert the select to original value on error
                        selectElement.val(selectElement.data('original-value'));
                    }
                });
            });

            // Store original values on page load
            $('.status-select').each(function() {
                $(this).data('original-value', $(this).val());
            });

            $('.payment-verification-select').each(function() {
                $(this).data('original-value', $(this).val());
            });

            $('.payment-type-select').each(function() {
                $(this).data('original-value', $(this).val());
            });
        });
    </script>
@endpush

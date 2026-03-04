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

        /* Auto-refresh indicator */
        .refresh-indicator {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            display: none;
        }

        .refresh-indicator.active {
            display: block;
        }

        .refresh-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .last-updated {
            position: fixed;
            top: 100px;
            left: 20px;
            z-index: 9999;
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-size: 12px;
            color: #666;
        }
    </style>
@endpush
@section('content')
    <!-- Refresh indicator -->
    <div class="refresh-indicator" id="refreshIndicator">
        <div class="refresh-spinner"></div>
    </div>
    <div class="last-updated" id="lastUpdated">
        Last updated: --
    </div>

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
            // Function to get current filter params from URL
            function getFilterParams() {
                const urlParams = new URLSearchParams(window.location.search);
                console.log(urlParams);
                return {
                    status: urlParams.get('status') || '',
                    order_code: urlParams.get('order_code') || '',
                    table_slug: urlParams.get('table_slug') || '',
                    order_type: urlParams.get('order_type') || '',
                    order_sort: urlParams.get('order_sort') || ''
                };
            }

            // Function to render orders table
            function renderOrdersTable(orders) {
                const tbody = $('tbody');
                tbody.empty();

                if (orders.length === 0) {
                    tbody.html('<tr><td colspan="9" class="text-center">No orders found.</td></tr>');
                    return;
                }

                orders.forEach(function(order) {
                    const row = `
                        <tr>
                            <td><a href="${order.urls.show}">${order.order_code}</a></td>
                            <td>${order.created_at}</td>
                            <td>${order.table}</td>
                            <td>${order.order_type}</td>
                            <td>${order.total_price} THB</td>
                            <td>${order.total_qty}</td>
                            <td>
                                <select class="form-select status-select" data-order-id="${order.id}"
                                    data-url="${order.urls.update_status}" style="width:auto;">
                                    <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="preparing" ${order.status === 'preparing' ? 'selected' : ''}>Preparing</option>
                                    <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                                    <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Completed</option>
                                    <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-select payment-type-select" data-order-id="${order.id}"
                                    data-url="${order.urls.update_payment_type}" style="width:auto;">
                                    <option value="" ${order.is_unpaid ? 'selected' : ''}>Unpaid</option>
                                    <option value="cash" ${order.is_payment_cash ? 'selected' : ''}>Cash</option>
                                    <option value="online" ${order.is_payment_online ? 'selected' : ''}>Online</option>
                                </select>
                            </td>
                            <td>
                                ${order.payment_image_path
                                    ? `<a href="${order.urls.payment_image}" class="text-decoration-underline" target="_blank">Check Payment</a>`
                                    : '-'}
                            </td>
                            <td>
                                ${(order.is_paid || order.is_payment_cash || order.is_payment_online)
                                    ? `<select class="form-select payment-verification-select"
                                        data-order-id="${order.id}" style="width:auto;">
                                        <option value="false" ${!order.is_payment_verified ? 'selected' : ''}>Pending</option>
                                        <option value="true" ${order.is_payment_verified ? 'selected' : ''}>Verified</option>
                                    </select>`
                                    : '-'}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                // Re-attach event handlers for the new elements
                attachEventHandlers();

                // Store original values
                $('.status-select').each(function() {
                    $(this).data('original-value', $(this).val());
                });
                $('.payment-verification-select').each(function() {
                    $(this).data('original-value', $(this).val());
                });
                $('.payment-type-select').each(function() {
                    $(this).data('original-value', $(this).val());
                });
            }

            // Function to fetch orders via AJAX
            function fetchOrders() {
                const filterParams = getFilterParams();
                const queryString = new URLSearchParams(filterParams).toString();

                $('#refreshIndicator').addClass('active');

                $.ajax({
                    url: '{{ route("orders.fetch") }}?' + queryString,
                    type: 'GET',
                    success: function(response) {
                        renderOrdersTable(response.orders);
                        $('#lastUpdated').text('Last updated: ' + new Date().toLocaleTimeString());
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch orders:', xhr);
                        toastr.error('Failed to fetch updated orders.');
                    },
                    complete: function() {
                        $('#refreshIndicator').removeClass('active');
                    }
                });
            }

            // Attach event handlers
            function attachEventHandlers() {
                $('.payment-type-select').off('change').on('change', function() {
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
                            var paymentImageCell = row.find('td').eq(8);

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

                $('.status-select').off('change').on('change', function() {
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
                            selectElement.val(selectElement.data('original-value'));
                        }
                    });
                });

                $('.payment-verification-select').off('change').on('change', function() {
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
                        },
                        error: function(xhr) {
                            toastr.error('Failed to update payment verification.');
                            selectElement.val(selectElement.data('original-value'));
                        }
                    });
                });
            }

            // Initialize event handlers on page load
            attachEventHandlers();

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

            // Auto-refresh every 30 seconds
            setInterval(fetchOrders, 30000);
        });
    </script>
@endpush

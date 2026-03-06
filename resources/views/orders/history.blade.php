@extends('layouts.app', ['elementActive' => 'order-history'])

@push('css')

@endpush
@section('content')
    <div class="card">
        <div class="card-header my-3">
            <h2 class="text-center">Order History</h2>
        </div>
        @include('filters.order-history-filter')

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
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.history.show', $order->id) }}" class="text-decoration-none">{{ $order->order_code }}</a></td>
                            <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $order->table_name ?? 'N/A' }}</td>
                            <td>{{ $order->order_type }}</td>
                            <td>{{ number_format($order->total_price, 2) }} THB</td>
                            <td>{{ $order->total_qty }}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $orders->appends(request()->query())->links() }}
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

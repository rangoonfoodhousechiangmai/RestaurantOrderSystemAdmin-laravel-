@extends('layouts.app', [
    'elementActive' => 'dashboard',
])

@section('content')
    <div class="row gap-3">
        <div class="col-12 mt-2">
            <h1 class="text-center fw-bold">Dashboard</h1>
        </div>
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line me-2"></i>
                        <h5 class="mb-0">Most Selling Items</h5>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-light active" id="weekly-btn">Weekly</button>
                        <button type="button" class="btn btn-sm btn-outline-light" id="monthly-btn">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody id="order-items-tbody">
                                @php
                                    $index = 1;
                                @endphp
                                @foreach ($orderItems as $item)
                                    <tr>
                                        <td>{{ $index }}.</td>
                                        <td>{{ $item->mm_name }}</td>
                                        <td><span class="badge bg-success">{{ $item->total_sold_quantity }}</span></td>
                                    </tr>
                                    @php
                                        $index++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="card shadow border-0">
                <h2 class="card-header bg-primary text-white">Today's Summary</h2>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h5>Orders Count</h5>
                            <p class="display-6">{{ $todayOrderItemCount }}</p>
                        </div>
                        <div class="col-6">
                            <h5>Total Revenue</h5>
                            <p class="display-6">THB {{ number_format($todayTotalRevenue, 0, '.', ',') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#weekly-btn').on('click', function() {
                $(this).addClass('active');
                $('#monthly-btn').removeClass('active');
                loadOrderItems('weekly');
            });

            $('#monthly-btn').on('click', function() {
                $(this).addClass('active');
                $('#weekly-btn').removeClass('active');
                loadOrderItems('monthly');
            });

            function loadOrderItems(period) {
                $.ajax({
                    url: '{{ route('dashboard.data', ':period') }}'.replace(':period', period),
                    method: 'GET',
                    success: function(response) {
                        updateTable(response.orderItems);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading data:', error);
                        toastr.error('Failed to load data');
                    }
                });
            }

            function updateTable(orderItems) {
                let tbody = $('#order-items-tbody');
                tbody.empty();

                orderItems.forEach(function(item, index) {
                    let row = `
                <tr>
                    <td>${index + 1}.</td>
                    <td>${item.mm_name}</td>
                    <td><span class="badge bg-success">${item.total_sold_quantity}</span></td>
                </tr>
            `;
                    tbody.append(row);
                });
            }
        });
    </script>
@endpush

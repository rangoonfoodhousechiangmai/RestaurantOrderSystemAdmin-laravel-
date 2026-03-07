@extends('layouts.app', [
    'elementActive' => 'call-waiter',
])

@section('content')
    <div class="card">
        <div class="card-header my-3">
            <h2 class="text-center">Waiter Call List</h2>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mt-2" id="waiterCallTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ !$status || $status == 'pending' ? 'active' : '' }}"
                    href="{{ route('waiter-call-list', ['status' => 'pending']) }}">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'done' ? 'active' : '' }}"
                    href="{{ route('waiter-call-list', ['status' => 'done']) }}">Done</a>
            </li>
        </ul>

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Table Number</th>
                        <th>Status</th>
                        <th>Change Status</th>
                        <th>Called At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waiterCalls as $index => $call)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $call->table->slug ?? 'N/A' }}</td>
                            <td>
                                @if ($call->status === "pending")
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif ($call->status === 'done')
                                    <span class="badge bg-success">Done</span>
                                @endif
                            </td>
                            <td>
                                <select class="form-select status-select" data-waiter-call-id="{{ $call->id }}"
                                    data-url="{{ route('waiter-calls.update-status', $call) }}" style="width:auto;">
                                    <option value="pending" {{ $call->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="done" {{ $call->status == 'done' ? 'selected' : '' }}>Done
                                    </option>
                                </select>
                            </td>
                            <td>{{ $call->created_at->format('Y-m-d H:i:s') }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No waiter calls found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $waiterCalls->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Store original values on page load
            $('.status-select').each(function() {
                $(this).data('original-value', $(this).val());
            });

            // Handle status change
            $('.status-select').off('change').on('change', function() {
                var waiterCallId = $(this).data('waiter-call-id');
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
                        window.location.reload();
                    },
                    error: function(xhr) {
                        toastr.error('Failed to update waiter call status.');
                        selectElement.val(selectElement.data('original-value'));
                    }
                });
            });
        });
    </script>
@endpush


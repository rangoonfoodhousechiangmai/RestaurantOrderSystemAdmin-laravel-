@extends('layouts.app', [
    'elementActive' => 'tables',
])

@section('content')
    <div>
        <h2 class="text-center my-3">Table</h2>

        <div class="d-flex flex-md-row flex-column-reverse justify-content-between mb-3">
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-success text-white border" data-bs-toggle="modal"
                    data-bs-target="#createTableModal">
                    Create
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        {{-- <th>No</th> --}}
                        <th>Table Number</th>
                        <th>Slug</th>
                        <th>QR Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @php
                        $index = ($tables->currentPage() - 1) * $tables->perPage() + 1;
                    @endphp --}}
                    @forelse($tables as $table)
                        <tr>
                            {{-- <td>{{ $index }}</td> --}}
                            <td>{{ $table->table_number }}</td>
                            <td>{{ $table->slug }}</td>
                            <td>
                                @if($table->qr_code_path)
                                    <a href="{{ Storage::url($table->qr_code_path) }}" target="_blank" class="btn btn-sm btn-info">
                                        View QR
                                    </a>
                                {{-- @else
                                    <button class="btn btn-sm btn-secondary" onclick="generateQrCode({{ $table->id }})">
                                        <i class="fa-solid fa-qrcode"></i> Generate QR
                                    </button> --}}
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal"
                                    data-bs-target="#editTableModal" onclick="editTable({{ $table->id }})">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('tables.destroy', $table->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white"
                                        onclick="return confirm('Are you sure you want to delete this table?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        {{-- @php $index++; @endphp --}}
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No tables found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $tables->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Create Table Modal -->
    <div class="modal fade" id="createTableModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createTableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tables.store') }}" method="POST" id="createTableForm" class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="table_number" class="form-label">Table Number <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="table_number" name="table_number">
                            <div class="invalid-feedback" data-error-for="table_number"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Table</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Table Modal -->
    <div class="modal fade" id="editTableModal" tabindex="-1" aria-labelledby="editTableModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTableForm" action="#" method="POST" class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_table_number" class="form-label">Table Number</label>
                            <input type="number" class="form-control" id="edit_table_number" name="table_number">
                            <div class="invalid-feedback" data-error-for="table_number"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">QR Code</label>
                            <div>
                                <img src="" id="qr_code_path" alt="">
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-info" id="regenerateQrBtn" onclick="regenerateQrCode()">
                                    Regenerate QR Code
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Table</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let currentTableId = null;

        function editTable(id) {
            currentTableId = id;
            $.ajax({
                url: '{{ route('tables.show', ':id') }}'.replace(':id', id),
                type: 'GET',
                success: function(data) {
                    const form = document.getElementById('editTableForm');
                    form.action = '{{ route('tables.update', ':id') }}'.replace(':id', id);
                    document.getElementById('edit_table_number').value = data.table_number;
                    $('#qr_code_path').attr('src', '{{ Storage::url('') }}' + data.qr_code_path);
                },
                error: function() {
                    toastr.error('Failed to fetch table data.', 'Error');
                }
            });
        }

        function regenerateQrCode() {
            if (!currentTableId) {
                toastr.error('No table selected.', 'Error');
                return;
            }

            $.ajax({
                url: '{{ route('tables.regenerate-qr', ':id') }}'.replace(':id', currentTableId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    toastr.success('QR Code regenerated successfully.', 'Success');
                    location.reload();
                },
                error: function() {
                    toastr.error('Failed to regenerate QR code.', 'Error');
                }
            });
        }



        $(document).ready(function() {
            // Clear create modal form when closed
            $('#createTableModal').on('hidden.bs.modal', function() {
                const form = $('#createTableForm')[0];
                form.reset();
                $(this).find('.invalid-feedback').text('');
                $(this).find('.form-control').removeClass('is-invalid');
            });

            // Clear edit modal form when closed
            $('#editTableModal').on('hidden.bs.modal', function() {
                const form = $('#editTableForm')[0];
                form.reset();
                $(this).find('.invalid-feedback').text('');
                $(this).find('.form-control').removeClass('is-invalid');
            });

            // Remove any stuck backdrops if modal fails to close properly
            $(document).on('click', '.modal-backdrop', function() {
                $('.modal-backdrop').remove();
                $('modal-backdrop.show').remove();
                $('body').removeClass('modal-open');
            });
        });
    </script>
@endpush

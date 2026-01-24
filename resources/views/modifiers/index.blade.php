@extends('layouts.app', [
    'elementActive' => 'modifiers',
])

@section('content')
    <div>
        <h2 class="text-center my-3">Modifier</h2>

        <div class="d-flex flex-md-row flex-column-reverse justify-content-between mb-3">
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-success text-white border" data-bs-toggle="modal"
                    data-bs-target="#createModifierModal">
                    Create
                </button>
            </div>

            @include('filters.modifier-filter')
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Modifier Type</th>
                        <th>Price</th>
                        <th>Selection Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $index = ($modifiers->currentPage() - 1) * $modifiers->perPage() + 1;
                    @endphp
                    @forelse($modifiers as $modifier)
                        <tr>
                            <td>{{ $index }}</td>
                            <td>{{ $modifier->eng_name }}<br><br>{{ $modifier->mm_name }}</td>
                            <td>{{ ucfirst($modifier->type) }}</td>
                            <td>{{ $modifier->price ?? '-' }}</td>
                            <td>{{ ucfirst($modifier->selection_type) }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal"
                                    data-bs-target="#editModifierModal" onclick="editModifier({{ $modifier->id }})">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('modifiers.destroy', $modifier->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white"
                                        onclick="return confirm('Are you sure you want to delete this modifier?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @php $index++; @endphp
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No modifiers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $modifiers->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Create Modifier Modal -->
    <div class="modal fade" id="createModifierModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="createModifierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Modifier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('modifiers.store') }}" method="POST" id="createModifierForm" class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="eng_name" class="form-label">English Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eng_name" name="eng_name" required>
                            <div class="invalid-feedback" data-error-for="eng_name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="emm_ame" class="form-label">Myanmar Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mm_name" name="mm_name" required>
                            <div class="invalid-feedback" data-error-for="mm_name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Modifier Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="" disabled selected>Select Modifier Type</option>

                                <option value="protein">Protein</option>
                                <option value="avoid">Avoid</option>
                                <option value="addon">Addon</option>
                                <option value="flavor">Flavor</option>
                            </select>
                            <div class="invalid-feedback" data-error-for="type"></div>
                        </div>
                        <div class="mb-3" id="price_div">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" min="0">
                            <div class="invalid-feedback" data-error-for="price"></div>
                        </div>
                        <div class="mb-3">
                            <label for="selection_type" class="form-label">Choose Selection Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="selection_type" name="selection_type" required>
                                <option value="" disabled selected>Choose Selection Type</option>
                                <option value="single">Single</option>
                                <option value="multiple">Multiple</option>
                            </select>
                            <div class="invalid-feedback" data-error-for="selection_type"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modifier Modal -->
    <div class="modal fade" id="editModifierModal" tabindex="-1" aria-labelledby="editModifierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Modifier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editModifierForm" action="#" method="POST" class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_eng_name" class="form-label">English Name</label>
                            <input type="text" class="form-control" id="edit_eng_name" name="edit_eng_name" required>
                            <div class="invalid-feedback" data-error-for="eng_name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_mm_name" class="form-label">Myanmar Name</label>
                            <input type="text" class="form-control" id="edit_mm_name" name="edit_mm_name" required>
                            <div class="invalid-feedback" data-error-for="mm_name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_type" class="form-label">Modifier Type</label>
                            <select class="form-control" id="edit_type" name="edit_type" required>
                                <option value="">Select Type</option>
                                <option value="protein">Protein</option>
                                <option value="avoid">Avoid</option>
                                <option value="addon">Addon</option>
                                <option value="flavor">Flavor</option>
                            </select>
                            <div class="invalid-feedback" data-error-for="edit_type"></div>
                        </div>
                        <div class="mb-3" id="edit_price_div">
                            <label for="edit_price" class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_price" name="edit_price"
                                min="0">
                            <div class="invalid-feedback" data-error-for="edit_price"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_selection_type" class="form-label">Choose Selection Type</label>
                            <select class="form-control" id="edit_selection_type" name="edit_selection_type" required>
                                <option value="" disabled selected>Choose Selection Type</option>
                                <option value="single">Single</option>
                                <option value="multiple">Multiple</option>

                            </select>
                            <div class="invalid-feedback" data-error-for="edit_selection_type"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function editModifier(id) {
            $.ajax({
                url: '{{ route('modifiers.show', ':id') }}'.replace(':id', id),
                type: 'GET',
                success: function(data) {
                    const form = document.getElementById('editModifierForm');
                    form.action = '{{ route('modifiers.update', ':id') }}'.replace(':id', id);
                    $('#edit_eng_name').val(data.eng_name);
                    $('#edit_mm_name').val(data.mm_name);
                    $('#edit_type').val(data.type).trigger('change');
                    $('#edit_selection_type').val(data.selection_type).trigger('change');
                    $('#edit_price').val(data.price || 0);
                    // if (data.type === 'addon') {
                    //     $('#edit_price').prop('disabled', false);
                    //     $('#edit_price').prop('required', true);
                    //     $('#edit_price_div').show();
                    // } else {
                    //     $('#edit_price').prop('disabled', true);
                    //     $('#edit_price_div').hide();
                    // }

                    // $('#edit_type').on('change', function() {
                    //     if ($('#edit_type').val() === 'addon') {
                    //         $('#edit_price').prop('disabled', false);
                    //         $('#edit_price').prop('required', true);
                    //         $('#edit_price_div').show();
                    //     } else {

                    //         $('#edit_price').prop('disabled', true);
                    //         $('#edit_price_div').hide();

                    //     }
                    // })


                },
                error: function() {
                    toastr.error('Failed to fetch modifier data.', 'Error');
                }
            });
        }

        $(document).ready(function() {
            // $('#price_div').hide();
            // $('#type').on('change', function() {
            //     if ($('#type').val() === 'addon') {
            //         $('#price').prop('disabled', false);
            //         $('#price').prop('required', true);
            //         $('#price_div').show();
            //     } else {
            //         $('')
            //         $('#price').prop('disabled', true);
            //         $('#price_div').hide();

            //     }
            // })


            // Clear create modal form when closed
            $('#createModifierModal').on('hidden.bs.modal', function() {
                const form = $('#createModifierForm')[0];
                form.reset();
                $(this).find('.invalid-feedback').text('');
                $(this).find('.form-control').removeClass('is-invalid');
            });

            // Clear edit modal form when closed
            $('#editModifierModal').on('hidden.bs.modal', function() {
                const form = $('#editModifierForm')[0];
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

            // Initialize select2 for category_id in createMenu modal with dropdownParent to fix modal conflict
            $('#createModifierModal').on('shown.bs.modal', function() {
                $('#type').select2({
                    dropdownParent: $('#createModifierModal')
                });
            });

            $('#createModifierModal').on('shown.bs.modal', function() {
                $('#selection_type').select2({
                    dropdownParent: $('#createModifierModal')
                });
            });

            $('#editModifierModal').on('shown.bs.modal', function() {
                $('#edit_type').select2({
                    dropdownParent: $('#editModifierModal')
                });
            });

            $('#editModifierModal').on('shown.bs.modal', function() {
                $('#edit_selection_type').select2({
                    dropdownParent: $('#editModifierModal')
                });
            });


        });
    </script>
@endpush

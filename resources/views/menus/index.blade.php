@extends('layouts.app', [
    'elementActive' => 'menus',
])

@section('css')
    <style>
        input[type="file"]:focus {
            background-color: #0e2443;
            /* or any color you want */
            border: 1px solid #ccc;
            /* optional, for visible focus */
        }
    </style>
@endsection
@section('content')
    <div>
        <h2 class="text-center my-3">Menu</h2>

        <div class="d-flex flex-md-row flex-column-reverse justify-content-between mb-3">
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-success text-white border" data-bs-toggle="modal"
                    data-bs-target="#createMenuModal">
                    Create
                </button>
            </div>

            @include('filters.menu-filter')
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Name</th>

                        <th>Price</th>
                        <th>Is Available</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $index = ($menus->currentPage() - 1) * $menus->perPage() + 1;
                    @endphp
                    @forelse($menus as $menu)
                        <tr>
                            <td>{{ $index }}</td>
                            <td>{{ $menu->eng_name }}<br><br>{{ $menu->mm_name }}</td>
                            <td>{{ $menu->price ? $menu->price : '-' }}</td>
                            <td>
                                @if ($menu->is_available)
                                    <span class="badge rounded-pill text-bg-success text-white fw-bolder">Yes</span>
                                @else
                                    <span class="badge rounded-pill text-bg-danger text-white fw-bolder">No</span>
                                @endif
                            </td>
                            <td>
                                @if ($menu->image_path && file_exists(public_path('storage/' . $menu->image)))
                                    <a href="{{ asset('storage/' . $menu->image_path) }}" target="blank">
                                        <img src="{{ asset('storage/' . $menu->image_path) }}"
                                            style="width: 100px; height: 100px; border-radius: 0"></a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal"
                                    data-bs-target="#editMenuModal" onclick="editMenu({{ $menu->id }})">
                                    <i class="far fa-edit"></i>
                                </button>
                                <form action="{{ route('menus.destroy', $menu->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white"
                                        onclick="return confirm('Are you sure you want to delete this menu?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                <a href="{{ route('menus.modifiers.create', $menu) }}" type="button"
                                    class="btn btn-sm btn-primary me-2">
                                    Update Modifiers
                                </a>
                                {{-- <form action="{{ route('menus.destroy', $category->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white"
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form> --}}
                            </td>
                        </tr>
                        @php $index++; @endphp
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No menus found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-start mt-3">
            {{ $menus->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="createMenuModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="createMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data" id="createMenuForm"
                    class="form-submit">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="eng_name" class="form-label">English Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eng_name" name="eng_name" required>
                                <div class="invalid-feedback" data-error-for="eng_name"></div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="mm_name" class="form-label">Myanmar Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mm_name" name="mm_name" required>
                                <div class="invalid-feedback" data-error-for="mm_name"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" selected disabled>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->eng_name }} /
                                            {{ $category->mm_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" data-error-for="category_id"></div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control form-control-sm" id="price" name="price"
                                    min="0" step="1">
                                <div class="invalid-feedback" data-error-for="price"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image_path" class="form-label">Image</label>
                            <input type="file" class="form-control text-white" id="image_path" name="image_path"
                                accept="image/*">
                            <div class="invalid-feedback" data-error-for="image_path"></div>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" class="form-check-input" id="is_available" name="is_available"
                                checked>
                            <label class="form-check-label" for="is_available">Is Available</label>
                            <div class="invalid-feedback" data-error-for="is_available"></div>

                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                            <label for="eng_description" class="form-label">English Description</label>
                            <textarea name="eng_description" id="" cols="30" rows="10" class="form-control"></textarea>
                            <div class="invalid-feedback" data-error-for="eng_description"></div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="mm_description" class="form-label">Myanmar Description</label>
                            <textarea name="mm_description" id="" cols="30" rows="10" class="form-control"></textarea>
                            <div class="invalid-feedback" data-error-for="mm_description"></div>
                        </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="editMenuForm" action="#" method="POST" class="form-submit">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="edit_eng_name" class="form-label">English Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_eng_name" name="edit_eng_name"
                                    required>
                                <div class="invalid-feedback" data-error-for="edit_eng_name"></div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="edit_mm_name" class="form-label">Myanmar Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_mm_name" name="edit_mm_name"
                                    required>
                                <div class="invalid-feedback" data-error-for="edit_mm_name"></div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="edit_category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="edit_category_id" name="edit_category_id" required>
                                    <option value="" disabled>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option {{ $category->id == $category->category_id ? 'selected' : '' }}
                                            value="{{ $category->id }}">{{ $category->eng_name }} /
                                            {{ $category->mm_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" data-error-for="edit_category_id"></div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="edit_price" class="form-label">Price</label>
                                <input type="number" class="form-control form-control-sm" id="edit_price"
                                    name="edit_price" min="0" step="1">
                                <div class="invalid-feedback" data-error-for="edit_price"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image_path" class="form-label">Image</label>
                            <div id="image_container" class="mb-2">

                            </div>
                            <input type="file" class="form-control form-control-dark text-white" id="edit_image_path"
                                name="edit_image_path" accept="image/*">
                            <div class="invalid-feedback" data-error-for="edit_image_path"></div>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" class="form-check-input" id="edit_is_available"
                                name="edit_is_available">
                            <label class="form-check-label" for="edit_is_available">Is Available</label>
                            <div class="invalid-feedback" data-error-for="edit_is_available"></div>

                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="edit_eng_description" class="form-label">English Description</label>
                                <textarea name="edit_eng_description" id="edit_eng_description" rows="10" class="form-control"></textarea>
                                <div class="invalid-feedback" data-error-for="edit_eng_description"></div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="edit_mm_description" class="form-label">Myanmar Description</label>
                                <textarea name="edit_mm_description" id="edit_mm_description" rows="10" class="form-control"></textarea>
                                <div class="invalid-feedback" data-error-for="edit_mm_description"></div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function editMenu(id) {
            $.ajax({
                url: '{{ route('menus.show', ':id') }}'.replace(':id', id),
                type: 'GET',
                success: function(data) {
                    const form = document.getElementById('editMenuForm');
                    form.action = '{{ route('menus.update', ':id') }}'.replace(':id', id);
                    $('#edit_category_id').val(data.category_id).trigger('change');
                    $('#edit_eng_name').val(data.eng_name);
                    $('#edit_mm_name').val(data.mm_name);
                    $('#edit_price').val(data.price);
                    $('#edit_eng_description').val(data.eng_description);
                    $('#edit_mm_description').val(data.mm_description);
                    $('#edit_is_available').prop('checked', data.is_available);

                    if (data.image_path) {
                        let baseUrl = "{{ asset('storage') }}";
                        $('#image_container').html(
                            '<img src="' + baseUrl + '/' + data.image_path +
                            '" style="width:100px;height:100px;">'
                        );
                    }
                },
                error: function() {
                    toastr.error('Failed to fetch menu data.', 'Error');
                }
            });
        }
        // Clear create menu modal form and validation on close
        $('#createMenuModal').on('hidden.bs.modal', function() {
            const form = $('#createMenuForm')[0];
            form.reset();
            $(this).find('.invalid-feedback').text('');
            $(this).find('.form-control').removeClass('is-invalid');
        });

        // Initialize select2 for category_id in createMenu modal with dropdownParent to fix modal conflict
        $('#createMenuModal').on('shown.bs.modal', function() {
            $('#category_id').select2({
                dropdownParent: $('#createMenuModal')
            });
        });

        // Initialize select2 for category_id in editMenu modal with dropdownParent to fix modal conflict
        $('#editMenuModal').on('shown.bs.modal', function() {
            $('#edit_category_id').select2({
                dropdownParent: $('#editMenuModal')
            });
        });
    </script>
@endpush

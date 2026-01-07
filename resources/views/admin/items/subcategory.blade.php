@extends('layouts.admin')
@section('title', 'Item Subcategory')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">
    <h2>Item Subcategory</h2>
    <p>Manage item subcategories here.</p>

    {{-- Add Subcategory Form --}}
    <div class="card mt-3 p-3">
        <form action="{{ route('admin.items.subcategories.store') }}" method="POST">
            @csrf
            <div class="form-group mb-2">
                <label for="category_id">Select Category</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-2">
                <label for="name">Subcategory Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Enter subcategory name">
            </div>

            <button type="submit" class="btn btn-primary mt-2">Add Subcategory</button>
        </form>
    </div>

    {{-- List of Subcategories --}}
    <div class="card mt-4 p-3">
        <h4>Subcategory List</h4>

        <table class="table table-bordered mt-2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subcategory Name</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($subcategories as $subcategory)
                    <tr>
                        <td>{{ $subcategory->id }}</td>
                        <td>{{ $subcategory->name }}</td>
                        <td>{{ $subcategory->category->name ?? 'N/A' }}</td>
                        <td>

                            <!-- EDIT BUTTON -->
                            <button class="btn btn-warning btn-sm"
                                onclick="openEditModal(
                                    {{ $subcategory->id }},
                                    '{{ $subcategory->name }}',
                                    {{ $subcategory->category_id }}
                                )">
                                Edit
                            </button>

                            <!-- DELETE -->
                            <form action="{{ route('admin.items.subcategories.destroy', $subcategory->id) }}"
                                  method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this subcategory?')">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>


<!-- ========================================================= -->
<!-- EDIT MODAL -->
<!-- ========================================================= -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-3">

            <div class="modal-header">
                <h5 class="modal-title">Edit Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="form-group mb-2">
                        <label>Select Category</label>
                        <select name="category_id" id="edit_category_id" class="form-control" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label>Subcategory Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Update</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Edit Script --}}
<script>
    function openEditModal(id, name, category_id) {

        // Set form action URL
        document.getElementById('editForm').action =
            "{{ url('/admin/items/subcategories/update') }}/" + id;

        // Fill form fields
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_category_id').value = category_id;

        // Show modal
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>

@endsection

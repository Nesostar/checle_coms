@extends('layouts.admin')
@section('title', 'Item Category')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container-fluid" style="padding: 20px;">

    <!-- Create Category Button -->
    <div style="margin-bottom: 15px;">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fa fa-plus"></i> Create new Category
        </button>
    </div>

    <!-- Table Panel -->
    <div class="card" style="border: 1px solid #ccc;">
        <div class="card-header" style="background: #1d6fb8; color: white; padding: 8px 15px; font-weight: bold;">
            Items / Product Category List
        </div>

        <div class="card-body" style="background: #fff;">
            <div class="table-responsive">
                <table id="categoryTable" class="table table-bordered table-striped table-sm" style="font-size: 14px;">
                    <thead>
                        <tr style="background: #198754; color: #fff;">
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Created Date</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->created_at->format('Y-m-d') }}</td>
                                <td>{{ strtoupper($category->created_by) }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ===================================== -->
<!-- MODAL: Create Category -->
<!-- ===================================== -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createCategoryLabel">Create New Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="createCategoryForm" method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" name="name" id="categoryName" class="form-control" placeholder="Enter category name" required>
                    </div>

                    <!-- Loading Bar -->
                    <div id="loadingBar" class="progress mt-3" style="display:none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:100%">
                            Please wait...
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div id="successMessage" class="alert alert-success mt-3" style="display:none;">
                        Category created successfully!
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveCategoryBtn" class="btn btn-success">
                        <i class="fa fa-save"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================== -->
<!-- SCRIPTS -->
<!-- ===================================== -->
@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('#categoryTable').DataTable();

    // Handle form submission with animation
    $('#createCategoryForm').on('submit', function(e) {
        e.preventDefault();
        $('#loadingBar').show();
        $('#saveCategoryBtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.categories.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#loadingBar').hide();
                $('#successMessage').fadeIn();
                setTimeout(function() {
                    $('#createCategoryModal').modal('hide');
                    location.reload(); // reload to show the new category
                }, 1200);
            },
            error: function() {
                $('#loadingBar').hide();
                alert('An error occurred. Please try again.');
                $('#saveCategoryBtn').prop('disabled', false);
            }
        });
    });
});
</script>
@endpush

@endsection

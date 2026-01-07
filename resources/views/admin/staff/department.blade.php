@extends('layouts.admin')
@section('title', 'Department List')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Department List</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDeptModal">
        + Add Department
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered table-striped" style="background: #e8f5e9;">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Department Name</th>
                <th>Users Assigned</th>
                <th>Created By</th>
                <th>Created At</th>
                <th width="150px">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($departments as $dept)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $dept->name }}</td>
                <td><span class="badge bg-info">{{ $dept->users_count }}</span></td>
                <td>{{ $dept->created_by ?? 'Admin' }}</td>
                <td>{{ $dept->created_at->format('d M Y') }}</td>

                <td>
                    <!-- EDIT MODAL -->
                    <button class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editDeptModal{{ $dept->id }}">
                        Edit
                    </button>

                    <!-- DELETE -->
                    <form action="{{ route('admin.departments.destroy', $dept->id) }}"
                          method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this department?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>

            <!-- EDIT MODAL -->
            <div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.departments.update', $dept->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Edit Department</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <label>Department Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $dept->name }}" required>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            @endforeach
        </tbody>
    </table>
</div>

<!-- ADD DEPARTMENT MODAL -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.departments.store') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add Department</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Department Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Create</button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection

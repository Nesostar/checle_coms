@extends('layouts.admin')
@section('title', 'Position List')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Position List</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPositionModal">
        + Add Position
    </button>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered table-striped table-success">
        <thead>
            <tr>
                <th>#</th>
                <th>Position Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($positions as $position)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $position->name }}</td>
                <td>
                    <!-- EDIT BUTTON -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editPositionModal{{ $position->id }}">Edit</button>

                    <!-- DELETE BUTTON -->
                    <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this position?')">Delete</button>
                    </form>
                </td>
            </tr>

            <!-- EDIT MODAL -->
            <div class="modal fade" id="editPositionModal{{ $position->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.positions.update', $position->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Edit Position</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Position Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $position->name }}" required>
                                </div>
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

<!-- ADD POSITION MODAL -->
<div class="modal fade" id="addPositionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.positions.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New Position</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Position Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Add Position</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

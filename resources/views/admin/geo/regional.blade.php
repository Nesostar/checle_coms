@extends('layouts.admin')
@section('title', 'Regional')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Regional</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRegionalModal">
        + Add Regional
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped table-success">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Regional Name</th>
            <th width="150px">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($regions as $regional)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $regional->name }}</td>
            <td>
                <!-- Edit -->
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRegionalModal{{ $regional->id }}">
                    Edit
                </button>

                <!-- Delete -->
                <form action="{{ route('admin.regionals.destroy', $regional->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this regional?')">Delete</button>
                </form>
            </td>
        </tr>

        <!-- Edit Modal -->
        <div class="modal fade" id="editRegionalModal{{ $regional->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.regionals.update', $regional->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5>Edit Regional</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>Regional Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $regional->name }}" required>
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

<!-- Add Modal -->
<div class="modal fade" id="addRegionalModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.regionals.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add Regional</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Regional Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

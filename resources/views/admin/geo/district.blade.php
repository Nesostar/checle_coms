@extends('layouts.admin')
@section('title', 'District')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>District</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDistrictModal">
        + Add District
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped table-success">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>District Name</th>
            <th width="150px">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($districts as $district)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $district->name }}</td>
            <td>
                <!-- Edit -->
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editDistrictModal{{ $district->id }}">
                    Edit
                </button>

                <!-- Delete -->
                <form action="{{ route('admin.geo.district.destroy', $district->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this district?')">Delete</button>
                </form>
            </td>
        </tr>

        <!-- Edit Modal -->
        <div class="modal fade" id="editDistrictModal{{ $district->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.geo.district.update', $district->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5>Edit District</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>District Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $district->name }}" required>
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
<div class="modal fade" id="addDistrictModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.district.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add District</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>District Name</label>
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

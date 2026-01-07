@extends('layouts.app')
@section('title', 'Expenditure Categories - Cashier COMS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container">
    <h2>Expenditure Categories</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('cashier.expenditure.category.store') }}" method="POST" class="mb-3">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Category Name" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="description" class="form-control" placeholder="Description (optional)">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Add Category</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr><th>#</th><th>Name</th><th>Description</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->description ?? '-' }}</td>
                    <td>
                        <form action="{{ route('cashier.expenditure.category.destroy', $cat->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No categories found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

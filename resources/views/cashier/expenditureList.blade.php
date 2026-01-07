@extends('layouts.app')
@section('title', 'Expenditure List - Cashier COMS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container">
    <h2>Expenditure List</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Add Expenditure Form -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">Add New Expenditure</div>
        <div class="card-body">
            <form action="{{ route('cashier.expenditure.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="title" class="form-control" placeholder="Expenditure Title" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="amount" class="form-control" step="0.01" placeholder="Amount" required>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success w-100">Add</button>
                    </div>
                </div>
                <div class="mt-2">
                    <textarea name="description" class="form-control" placeholder="Description (optional)"></textarea>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenditure Table -->
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenditures as $exp)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $exp->title }}</td>
                    <td>{{ $exp->category->name }}</td>
                    <td>{{ number_format($exp->amount,2) }}</td>
                    <td>{{ $exp->date }}</td>
                    <td>{{ $exp->description ?? '-' }}</td>
                    <td>
                        <form action="{{ route('cashier.expenditure.destroy', $exp->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this expenditure?')">Delete</button>
                        </form>
                        <!-- Optional: Add edit/update modal -->
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No expenditures found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

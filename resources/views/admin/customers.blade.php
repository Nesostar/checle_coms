@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<h3 class="mb-3">Manage Customers</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Add Customer Form -->
<div class="card mb-3">
    <div class="card-header bg-primary text-white">Add Customer</div>
    <div class="card-body">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
            <div class="row g-2">
                <div class="col-md-4 col-12 mb-2">
                    <input type="text" name="name" placeholder="Customer Name" class="form-control" required>
                </div>
                <div class="col-md-2 col-12 mb-2">
                    <input type="text" name="phone" placeholder="Phone" class="form-control">
                </div>
                <div class="col-md-3 col-12 mb-2">
                    <input type="email" name="email" placeholder="Email" class="form-control">
                </div>
            </div>
            <div class="mt-2">
                <textarea name="address" class="form-control" placeholder="Address"></textarea>
            </div>
            <button class="btn btn-success mt-2">Save Customer</button>
        </form>
    </div>
</div>

<!-- Customers List -->
<div class="card">
    <div class="card-header bg-dark text-white">Customers List</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->address }}</td>
                        <td>
                            <form action="{{ route('admin.customers.delete', $customer->id) }}" 
                                  method="POST" 
                                  style="display:inline;"
                                  onsubmit="return confirm('Delete customer?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
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
</div>
@endsection

@extends('layouts.app')

@section('title', 'Customers - Cashier COMS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<h3 class="mb-3">Customer Management</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Add Customer --}}
<div class="card mb-3">
    <div class="card-header bg-primary text-white">Add Customer</div>
    <div class="card-body">
        <form action="{{ route('cashier.customers.store') }}" method="POST">
            @csrf

            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Customer Name" required>
                </div>

                <div class="col-md-3">
                    <input type="text" name="contact_person" class="form-control" placeholder="Contact Person">
                </div>

                <div class="col-md-2">
                    <input type="text" name="phone" class="form-control" placeholder="Phone">
                </div>

                <div class="col-md-3">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                </div>
            </div>

            <div class="mt-2">
                <textarea name="address" class="form-control" placeholder="Address"></textarea>
            </div>

            <button class="btn btn-success mt-2">Save Customer</button>
        </form>
    </div>
</div>

{{-- Customers List --}}
<div class="card">
    <div class="card-header bg-dark text-white">Customers List</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th width="100">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->contact_person ?? '-' }}</td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td>{{ $customer->email ?? '-' }}</td>
                        <td>{{ $customer->address ?? '-' }}</td>
                        <td>
                            <form action="{{ route('cashier.customers.destroy', $customer->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this customer?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No customers found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

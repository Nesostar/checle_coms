@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<h3 class="mb-3">Manage Customers</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-3">
    <div class="card-header bg-primary text-white">Add Customer</div>
    <div class="card-body">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
            <div class="row g-2">
                <div class="col-md-4"><input type="text" name="name" placeholder="Customer Name" class="form-control" required></div>
                <div class="col-md-3"><input type="text" name="contact_person" placeholder="Contact Person" class="form-control"></div>
                <div class="col-md-2"><input type="text" name="phone" placeholder="Phone" class="form-control"></div>
                <div class="col-md-3"><input type="email" name="email" placeholder="Email" class="form-control"></div>
            </div>
            <div class="mt-2">
                <textarea name="address" class="form-control" placeholder="Address"></textarea>
            </div>
            <button class="btn btn-success mt-2">Save Customer</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-dark text-white">Customers List</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->contact_person }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>
                        <a href="{{ route('admin.customers.delete', $customer->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete customer?')">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

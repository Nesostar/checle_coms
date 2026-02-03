@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-3">

    <h3 class="mb-3">Manage Suppliers</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Add Supplier Form -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">Add Supplier</div>
        <div class="card-body">
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-4 col-12 mb-2">
                        <input type="text" name="name" placeholder="Supplier Name" class="form-control" required>
                    </div>
                    <div class="col-md-2 col-12 mb-2">
                        <input type="text" name="phone" placeholder="Phone" class="form-control">
                    </div>
                    <div class="col-md-3 col-12 mb-2">
                        <input type="email" name="email" placeholder="Email" class="form-control">
                    </div>
                    <div class="col-12 mb-2">
                        <textarea name="address" class="form-control" placeholder="Address"></textarea>
                    </div>
                </div>

                <button class="btn btn-success mt-2">Save Supplier</button>
            </form>
        </div>
    </div>

    <!-- Suppliers List -->
    <div class="card">
        <div class="card-header bg-dark text-white">Suppliers List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $sup)
                            <tr>
                                <td>{{ $sup->name }}</td>
                                <td>{{ $sup->phone }}</td>
                                <td>{{ $sup->email }}</td>
                                <td>{{ $sup->address }}</td>
                                <td>
                                    <form action="{{ route('admin.suppliers.delete', $sup->id) }}" 
                                          method="POST" 
                                          style="display:inline;"
                                          onsubmit="return confirm('Delete supplier?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete</button>
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
@endsection

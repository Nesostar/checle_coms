@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<h3 class="mb-3">Manage Quotations</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-3">
    <div class="card-header bg-primary text-white">Add / Edit Quotation</div>
    <div class="card-body">
        <form action="{{ route('admin.quotations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value=""> {{-- for editing, JS can fill this --}}
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="customer_id" class="form-control" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="quotation_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="total" placeholder="Total" class="form-control" step="0.01">
                </div>
            </div>

            <button class="btn btn-success mt-2">Save Quotation</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-dark text-white">Quotations List</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotations as $q)
                <tr>
                    <td>{{ $q->id }}</td>
                    <td>{{ $q->customer->name ?? 'N/A' }}</td>
                    <td>{{ $q->quotation_date }}</td>
                    <td>{{ $q->status }}</td>
                    <td>{{ $q->total }}</td>
                    <td>
                        <button class="btn btn-info btn-sm edit-btn" 
                                data-id="{{ $q->id }}"
                                data-customer="{{ $q->customer_id }}"
                                data-date="{{ $q->quotation_date }}"
                                data-status="{{ $q->status }}"
                                data-total="{{ $q->total }}">
                            Edit
                        </button>
                        <a href="{{ route('admin.quotations.delete', $q->id) }}" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete quotation?')">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // JS to populate edit form
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = document.querySelector('form');
            form.querySelector('[name=id]').value = this.dataset.id;
            form.querySelector('[name=customer_id]').value = this.dataset.customer;
            form.querySelector('[name=quotation_date]').value = this.dataset.date;
            form.querySelector('[name=status]').value = this.dataset.status;
            form.querySelector('[name=total]').value = this.dataset.total;
            window.scrollTo(0,0); // scroll to form
        });
    });
</script>
@endsection

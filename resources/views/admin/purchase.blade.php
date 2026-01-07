@extends('layouts.admin')

@section('title', 'Purchase - Admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container">

    <h2 class="mb-4">Purchase Management</h2>

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <!-- ==================== PURCHASE FORM ===================== -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Add New Purchase</strong>
        </div>

        <div class="card-body">

        <form action="{{ route('admin.purchase.store') }}" method="POST">

                @csrf

                <div class="row">

                    <div class="col-md-4">
                        <label><strong>Select Item</strong></label>
                        <select name="item_id" class="form-control" required>
                            <option value="">Choose Item</option>
                            @foreach ($items as $item)
                            <option value="{{ $item->id }}">
    {{ $item->name }} (Current Stock: {{ $item->getCurrentStock() }})
</option>

                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label><strong>Quantity</strong></label>
                        <input type="number" min="1" name="quantity" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label><strong>Purchase Price</strong></label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control">
                    </div>

                    <div class="col-md-3 mt-3">
                        <label><strong>Supplier</strong></label>
                        <input type="text" name="supplier" class="form-control">
                    </div>

                    <div class="col-md-3 mt-3">
                        <label><strong>Purchase Date</strong></label>
                        <input type="date" name="purchase_date" class="form-control" required>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label><strong>Notes (Optional)</strong></label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>

                </div>

                <button class="btn btn-success mt-3">Save Purchase</button>

            </form>

        </div>
    </div>


    <!-- ==================== PURCHASE LIST ===================== -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>Purchase History</strong>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Note</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase->item->name }}</td>
                            <td>{{ $purchase->quantity }}</td>
                            <td>{{ $purchase->purchase_price ?? 'N/A' }}</td>
                            <td>{{ $purchase->supplier ?? 'N/A' }}</td>
                            <td>{{ $purchase->purchase_date }}</td>
                            <td>{{ $purchase->note }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No purchase records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection

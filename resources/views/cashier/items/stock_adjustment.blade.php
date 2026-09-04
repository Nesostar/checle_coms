@extends('layouts.app')
@section('title', 'Stock Adjustment')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Stock Adjustment</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adjustModal">
        + Add Adjustment
    </button>
</div>

{{-- SUCCESS MESSAGE --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- TABLE OF TRANSACTIONS --}}
<div class="card">
    <div class="card-header">
        <strong>All Stock Adjustments</strong>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Entry Type</th>
                    <th>Quantity</th>
                    <th>Note</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td>{{ $t->item->name }}</td>
                        <td>{{ $t->entryType->name }}</td>

                        {{-- COLOR FOR + OR - --}}
                        <td class="{{ $t->quantity > 0 ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                            {{ $t->quantity }}
                        </td>

                        <td>{{ $t->note }}</td>
                        <td>{{ $t->created_at->format('d M, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            No stock adjustments recorded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ========================= --}}
{{--   ADD ADJUSTMENT MODAL   --}}
{{-- ========================= --}}
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">New Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('cashier.items.inventory.adjust.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- ITEM --}}
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <select name="item_id" class="form-select" required>
                            <option value="">-- Select Item --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} (Stock: {{ $item->getCurrentStock() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ENTRY TYPE --}}
                    <div class="mb-3">
                        <label class="form-label">Entry Type</label>
                        <select name="entry_type_id" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            @foreach ($entryTypes as $type)
                                <option value="{{ $type->id }}">
                                    {{ $type->name }} 
                                    ({{ $type->effect == '+' ? 'Increase' : 'Decrease' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- QUANTITY --}}
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                    {{-- NOTE --}}
                    <div class="mb-3">
                        <label class="form-label">Note (Optional)</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Save Adjustment</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@extends('layouts.admin')

@section('title', 'Paid Sales - COMS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    <h2 class="mb-3">Paid Sales</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-success text-white">
            Fully Paid Bills
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Date</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->customer_name ?? 'Walk-in' }}</td>
                            <td>{{ number_format($sale->total_amount, 2) }}</td>
                            <td>{{ number_format($sale->amount_paid, 2) }}</td>
                            <td>{{ number_format($sale->balance, 2) }}</td>
                            <td>{{ $sale->created_at->format('d M Y - h:i A') }}</td>
                            <td>
                                <button 
                                    class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#itemsModal{{ $sale->id }}">
                                    View Items
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                No paid sales available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

{{-- ===============================
    ITEMS MODALS (OUTSIDE TABLE)
=============================== --}}
@foreach($sales as $sale)
<div class="modal fade" id="itemsModal{{ $sale->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Items for Invoice: {{ $sale->invoice_no }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @if($sale->items->count())
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                <td>{{ $item->item->name ?? 'Item Deleted' }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No items for this sale.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection

@extends('layouts.admin')

@section('title', 'Pending Payments - COMS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container">

    <h2 class="mb-3">Incomplete Payment / Pending Bills</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-warning text-dark">
            Pending Payment Bills
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Date</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->customer_name ?? 'Walk-in' }}</td>

                            <td>{{ number_format($sale->total, 2) }}</td>
                            <td>{{ number_format($sale->amount_paid, 2) }}</td>
                            <td class="text-danger fw-bold">{{ number_format($sale->balance, 2) }}</td>

                            <td>{{ $sale->created_at->format('d M Y - h:i A') }}</td>

                            <td>
                                <!-- View Items Button -->
                                <button 
                                    class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#itemsModal{{ $sale->id }}">
                                    View Items
                                </button>

                                <!-- Pay Now Button -->
                                <button 
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#payModal{{ $sale->id }}">
                                    Pay Now
                                </button>
                            </td>
                        </tr>


                        <!-- ITEMS MODAL -->
                        <div class="modal fade" id="itemsModal{{ $sale->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Items for Invoice: {{ $sale->invoice_no }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($sale->items as $item)
                                                    <tr>
                                                        <td>{{ $item->item->name }}</td>
                                                        <td>{{ number_format($item->price, 2) }}</td>
                                                        <td>{{ $item->qty }}</td>
                                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- PAYMENT MODAL -->
                        <div class="modal fade" id="payModal{{ $sale->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <form action="{{ route('billing.pay_pending') }}" method="POST">
                                        @csrf

                                        <div class="modal-header">
                                            <h5 class="modal-title">Pay Remaining Balance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                                            <p><strong>Invoice:</strong> {{ $sale->invoice_no }}</p>
                                            <p><strong>Customer:</strong> {{ $sale->customer_name ?? 'Walk-in' }}</p>
                                            <p><strong>Total:</strong> {{ number_format($sale->total, 2) }}</p>
                                            <p><strong>Amount Paid:</strong> {{ number_format($sale->amount_paid, 2) }}</p>
                                            <p><strong>Remaining Balance:</strong> 
                                                <span class="text-danger fw-bold">
                                                    {{ number_format($sale->balance, 2) }}
                                                </span>
                                            </p>

                                            <label><strong>Enter Payment Amount</strong></label>
                                            <input 
                                                type="number" 
                                                name="amount"
                                                min="1"
                                                max="{{ $sale->balance }}"
                                                step="0.01"
                                                class="form-control"
                                                required>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-success">Submit Payment</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                No pending bills found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

@endsection

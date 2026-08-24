@extends('layouts.app')

@section('title', 'My Payments')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    <h3 class="mb-3">My Payment List</h3>

    <div class="card">
        <div class="card-header bg-success text-white">
            Payments Collected
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($payments as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p['type'] }}</td>
                            <td>{{ $p['reference'] }}</td>
                            <td>{{ $p['customer'] }}</td>
                            <td>{{ number_format($p['amount'], 2) }}</td>
                            <td>{{ number_format($p['paid'], 2) }}</td>
                            <td>{{ number_format($p['balance'], 2) }}</td>
                            <td>{{ $p['method'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($p['date'])->format('d M Y - h:i A') }}</td>
                            <td>
                                @if($p['type'] === 'POS Sale')
                                    <button
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#itemsModal{{ $i }}">
                                        View Items
                                    </button>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-3">
                                No payments recorded
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
@foreach($payments as $i => $p)
@if($p['type'] === 'POS Sale')
<div class="modal fade" id="itemsModal{{ $i }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Items — {{ $p['reference'] }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @if(count($p['items']))
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
                                @foreach($p['items'] as $item)
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
                    <p class="text-muted mb-0">No items found.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endif
@endforeach

@endsection

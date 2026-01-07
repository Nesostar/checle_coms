@extends('layouts.admin')
@section('title','Sales Report')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    {{-- Header + Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Sales Report</h2>

        <div>
            <a href="{{ route('admin.report.sales.pdf', request()->all()) }}"
               class="btn btn-success btn-sm">
                Download PDF
            </a>

            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                Print
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <form class="row g-2 mb-3" method="GET" action="{{ route('admin.report.sales') }}">
        <div class="col-auto">
            <input type="date" name="from_date" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-auto">
            <input type="date" name="to_date" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    {{-- Summary --}}
    <div class="alert alert-primary d-flex justify-content-between flex-wrap">
        <span><strong>Total Sales:</strong> {{ number_format($total,2) }}</span>
        <span><strong>Total Received:</strong> {{ number_format($paidTotal,2) }}</span>
        <span>
            <strong>Outstanding:</strong>
            {{ number_format($total - $paidTotal,2) }}
        </span>
    </div>

    {{-- Sales Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                @php
                    $sumTotal = 0;
                    $sumPaid = 0;
                    $sumBalance = 0;
                @endphp

                @forelse($sales as $sale)
                    @php
                        $sumTotal += $sale->total_amount;
                        $sumPaid += $sale->amount_paid;
                        $sumBalance += $sale->balance;
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->customer_name }}</td>
                        <td class="text-end">{{ number_format($sale->total_amount,2) }}</td>
                        <td class="text-end">{{ number_format($sale->amount_paid,2) }}</td>
                        <td class="text-end">{{ number_format($sale->balance,2) }}</td>
                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No sales found for selected period
                        </td>
                    </tr>
                @endforelse
                </tbody>

                <tfoot>
                    <tr class="table-success fw-bold">
                        <td colspan="2">Totals</td>
                        <td class="text-end">{{ number_format($sumTotal,2) }}</td>
                        <td class="text-end">{{ number_format($sumPaid,2) }}</td>
                        <td class="text-end">{{ number_format($sumBalance,2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

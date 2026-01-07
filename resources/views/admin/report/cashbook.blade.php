@extends('layouts.admin')
@section('title','Cash Book')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    {{-- Header + Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Cash Book</h2>

        <div>
            <a href="{{ route('admin.report.cashbook.pdf', request()->all()) }}"
               class="btn btn-success btn-sm">
                Download PDF
            </a>

            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                Print
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <form class="row g-2 mb-3" method="GET" action="{{ route('admin.report.cashbook') }}">
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
    <div class="alert alert-light border">
        <strong>Money IN:</strong> {{ number_format($moneyIn,2) }} |
        <strong>Money OUT:</strong> {{ number_format($moneyOut,2) }} |
        <strong>Balance:</strong>
        <span class="{{ $balance < 0 ? 'text-danger' : 'text-success' }}">
            {{ number_format($balance,2) }}
        </span>
    </div>

    <div class="row">

        {{-- MONEY IN --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    Money In – Sales & Incomes
                </div>

                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th class="text-end">Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>

                        @php $totalIn = 0; @endphp

                        @forelse($salesList as $s)
                            @php $totalIn += $s->amount_paid; @endphp
                            <tr>
                                <td>Sale {{ $s->id }}</td>
                                <td class="text-end">{{ number_format($s->amount_paid,2) }}</td>
                                <td>{{ $s->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                        @endforelse

                        @foreach($incomeList as $inc)
                            @php $totalIn += $inc->amount; @endphp
                            <tr>
                                <td>{{ $inc->title ?? ($inc->category->name ?? 'Income') }}</td>
                                <td class="text-end">{{ number_format($inc->amount,2) }}</td>
                                <td>{{ $inc->date }}</td>
                            </tr>
                        @endforeach

                        @if($totalIn == 0)
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No money-in records found
                                </td>
                            </tr>
                        @endif

                        </tbody>

                        <tfoot>
                            <tr class="table-success fw-bold">
                                <td>Total Money In</td>
                                <td class="text-end">{{ number_format($totalIn,2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- MONEY OUT --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-danger text-white">
                    Money Out – Expenses & Purchases
                </div>

                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th class="text-end">Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>

                        @php $totalOut = 0; @endphp

                        @foreach($expenseList as $e)
                            @php $totalOut += $e->amount; @endphp
                            <tr>
                                <td>Expense: {{ $e->title }}</td>
                                <td class="text-end">{{ number_format($e->amount,2) }}</td>
                                <td>{{ $e->date }}</td>
                            </tr>
                        @endforeach

                        @foreach($purchaseList as $p)
                            @php $totalOut += ($p->total_amount ?? 0); @endphp
                            <tr>
                                <td>Purchase {{ $p->id }}</td>
                                <td class="text-end">{{ number_format($p->total_amount ?? 0,2) }}</td>
                                <td>{{ $p->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach

                        @if($totalOut == 0)
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No money-out records found
                                </td>
                            </tr>
                        @endif

                        </tbody>

                        <tfoot>
                            <tr class="table-danger fw-bold">
                                <td>Total Money Out</td>
                                <td class="text-end">{{ number_format($totalOut,2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

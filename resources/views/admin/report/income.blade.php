@extends('layouts.admin')
@section('title','Income Report')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    {{-- Header + Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Income Report</h2>

        <div>
            <a href="{{ route('admin.report.income.pdf', request()->all()) }}"
               class="btn btn-success btn-sm">
                Download PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form class="row g-2 mb-3" method="GET" action="{{ route('admin.report.income') }}">
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
    <div class="alert alert-success">
        <strong>Total Income:</strong> {{ number_format($totalIncome,2) }}
    </div>

    {{-- Income Table --}}
    <div class="card mb-3">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th class="text-end">Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                @php $sum = 0; @endphp

                @forelse($incomes as $inc)
                    @php $sum += $inc->amount; @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $inc->title }}</td>
                        <td>{{ $inc->category->name ?? 'Uncategorized' }}</td>
                        <td class="text-end">{{ number_format($inc->amount,2) }}</td>
                        <td>{{ $inc->date }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No income records found
                        </td>
                    </tr>
                @endforelse
                </tbody>

                <tfoot>
                    <tr class="table-success fw-bold">
                        <td colspan="3">Total</td>
                        <td class="text-end">{{ number_format($sum,2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Income by Category --}}
    <div class="card">
        <div class="card-header fw-bold">
            Income by Category
        </div>

        <div class="card-body">
            @if($byCategory->isEmpty())
                <p class="text-muted text-center">No category data available</p>
            @else
                <ul class="list-group">
                    @foreach($byCategory as $cat => $amt)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $cat }}</span>
                            <span class="fw-bold">{{ number_format($amt,2) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

</div>
@endsection

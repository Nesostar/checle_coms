@extends('layouts.app')

@section('title', 'Expired Items')

@section('content')
<div class="container mt-4">

    <h2 class="mb-3">Expired Items</h2>
    <p class="text-muted">Below is a list of items that have expired stock batches.</p>

    @if ($expired->isEmpty())
        <div class="alert alert-success">
            🎉 No expired stock found!
        </div>
    @else

        <div class="alert alert-danger">
            ⚠️ {{ $expired->count() }} item(s) have expired stock.
        </div>

        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Total Expired Qty</th>
                    <th>Earliest Expiry</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($expired as $itemId => $transactions)
                    @php
                        $item = $transactions->first()->item;
                        $totalExpired = $transactions->sum('quantity');
                        $earliest = $transactions->min('expiry_date');
                    @endphp

                    <tr class="table-danger">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $totalExpired }}</td>
                        <td>{{ \Carbon\Carbon::parse($earliest)->format('d M Y') }}</td>

                        <td>
                        <a href="{{ route('cashier.items.stock') }}" class="btn btn-primary btn-sm">
                        Inventory
                        </a>

                    <a href="{{ route('cashier.items.adjustment') }}" class="btn btn-warning btn-sm">
                    Adjust Stock
                    </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    @endif

</div>
@endsection

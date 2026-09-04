@extends('layouts.app')

@section('title', 'Items Out of Stock')

@section('content')
<div class="container mt-4">

    <h2 class="mb-3">Items Out of Stock</h2>
    <p class="text-muted">Below is a list of all items that currently have zero or negative stock.</p>

    @if ($items->isEmpty())
        <div class="alert alert-success">
            🎉 Great! No items are out of stock.
        </div>
    @else
        <div class="alert alert-danger">
            ⚠️ {{ $items->count() }} item(s) are currently out of stock.
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Current Stock</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $item)
                    @php
                    $stock = $item->getCurrentStock();
                        if ($stock <= 0) {
                            $badge = 'bg-danger';
                            $stockMessage = 'Out of Stock';
                        } elseif ($stock < 10) {
                            $badge = 'bg-warning text-dark';
                            $stockMessage = 'Low Stock';
                        } else {
                            $badge = 'bg-success';
                            $stockMessage = 'In Stock';
                        }
                    @endphp
                    <tr class="{{ $stock <= 0 ? 'table-danger' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td>{{ $item->subcategory->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $badge }}">{{ $stock }} Kg ({{ $stockMessage }})</span>
                        </td>
                        <td>{{ optional($item->transactions()->latest()->first())->created_at?->format('d M Y') ?? 'N/A' }}</td>

                        <td>
                            <a href="{{ route('cashier.items.inventory.index') }}" class="btn btn-sm btn-primary">
                                View Inventory
                            </a>
                            <a href="{{ route('cashier.items.inventory.transactions', $item->id) }}" class="btn btn-sm btn-info">
                                View History
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    @endif
</div>
@endsection

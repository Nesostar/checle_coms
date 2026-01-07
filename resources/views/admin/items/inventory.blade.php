@extends('layouts.admin')
@section('title', 'Inventory / Stock')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    <h2>Inventory / Stock</h2>
    <p>Manage stock, prices, and expiry dates.</p>

    {{-- ADD STOCK --}}
    <div class="card mt-3 p-3">
        <h5>Add Stock Entry</h5>

        <form action="{{ route('admin.items.inventory.store') }}" method="POST">
            @csrf

            <div class="row">
                {{-- Item --}}
                <div class="col-md-4 mb-2">
                    <label>Item</label>
                    <select name="item_id" class="form-control" required>
                        <option value="">-- Select Item --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} ({{ $item->subcategory->name ?? 'No Subcategory' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Stock Type --}}
                <div class="col-md-4 mb-2">
                    <label>Stock Type</label>
                    <select name="entry_type_id" class="form-control" required>
                        <option value="">-- Select Stock Type --</option>
                        @foreach($entryTypes as $type)
                            <option value="{{ $type->id }}">{{ ucfirst($type->name) }} ({{ strtoupper($type->direction) }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Quantity --}}
                <div class="col-md-4 mb-2">
                    <label>Quantity(Kg)</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>

                {{-- Retail Price --}}
                <div class="col-md-4 mb-2">
                    <label>Retail Price(Tsh)</label>
                    <input type="number" step="0.01" name="retail_price" class="form-control">
                </div>

                {{-- Wholesale Price --}}
                <div class="col-md-4 mb-2">
                    <label>Wholesale Price(Tsh)</label>
                    <input type="number" step="0.01" name="whole_price" class="form-control">
                </div>

                {{-- Expiry Date --}}
                <div class="col-md-4 mb-2">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control">
                </div>

                {{-- Note --}}
                <div class="col-md-12 mb-2">
                    <label>Note</label>
                    <input type="text" name="note" class="form-control">
                </div>
            </div>

            <button class="btn btn-primary mt-2">Save Stock</button>
        </form>
    </div>

    {{-- STOCK TRANSACTIONS --}}
    <div class="card mt-4 p-3">
        <h4>Stock Transactions</h4>

        <table class="table table-bordered mt-2">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Stock After</th>
                    <th>Retail</th>
                    <th>Wholesale</th>
                    <th>Expiry</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @php
                    // Initialize balance tracker for each item
                    $itemBalances = [];
                    
                    // Sort transactions chronologically (oldest first) for proper balance calculation
                    $sortedTransactions = $transactions->sortBy([
                        ['created_at', 'asc'],
                        ['id', 'asc']
                    ]);
                @endphp
                
                @forelse($sortedTransactions as $transaction)
                @php
                    $itemId = $transaction->item_id;
                    
                    // Initialize balance for this item if not set
                    if (!isset($itemBalances[$itemId])) {
                        // For new items, start with 0
                        // If you have initial stock, you might want to:
                        // 1. Add an 'initial_quantity' field to items table
                        // 2. Start with: $transaction->item->initial_quantity ?? 0
                        $itemBalances[$itemId] = 0;
                    }
                    
                    // Get the entry type direction
                    $direction = $transaction->entryType->direction ?? 'in';
                    
                    // Calculate running balance
                    $oldBalance = $itemBalances[$itemId];
                    
                    switch($direction) {
                        case 'in':
                            $itemBalances[$itemId] += $transaction->quantity;
                            break;
                        case 'out':
                        case 'damage':
                            $itemBalances[$itemId] -= $transaction->quantity;
                            break;
                        case 'adjustment':
                            $itemBalances[$itemId] = $transaction->quantity;
                            break;
                    }
                    
                    $newBalance = $itemBalances[$itemId];
                @endphp
                
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->item->name ?? '-' }}</td>

                    {{-- Stock Type with color coding --}}
                    <td>
                        @php
                            $badgeColor = 'bg-secondary';
                            $direction = $transaction->entryType->direction ?? 'in';
                            
                            if ($direction === 'in') {
                                $badgeColor = 'bg-success';
                            } elseif ($direction === 'out') {
                                $badgeColor = 'bg-warning text-dark';
                            } elseif ($direction === 'damage') {
                                $badgeColor = 'bg-danger';
                            } elseif ($direction === 'adjustment') {
                                $badgeColor = 'bg-info';
                            }
                        @endphp
                        <span class="badge {{ $badgeColor }}">
                            {{ strtoupper($transaction->entryType->name ?? $direction) }}
                        </span>
                    </td>

                    <td>
                        @if(in_array($direction, ['out', 'damage']))
                            <span class="text-danger">-{{ $transaction->quantity }}</span>
                        @else
                            <span class="text-success">+{{ $transaction->quantity }}</span>
                        @endif
                    </td>

                    {{-- Stock After This Transaction --}}
                    <td>
                        <strong>{{ $newBalance }}</strong>
                        @if($direction !== 'adjustment' && $oldBalance != 0)
                            <small class="text-muted d-block">
                                ({{ $oldBalance }} → {{ $newBalance }})
                            </small>
                        @endif
                    </td>

                    {{-- Prices --}}
                    <td>{{ number_format($transaction->item?->retail_price ?? 0, 2) }}</td>
                    <td>{{ number_format($transaction->item?->whole_price ?? 0, 2) }}</td>

                    {{-- Expiry --}}
                    <td>
                        @if($transaction->expiry_date || $transaction->item?->expiry_date)
                            @php
                                $expiryDate = $transaction->expiry_date ?? $transaction->item?->expiry_date;
                                $isExpired = \Carbon\Carbon::parse($expiryDate)->isPast();
                            @endphp
                            <span class="{{ $isExpired ? 'text-danger' : 'text-success' }}">
                                {{ \Carbon\Carbon::parse($expiryDate)->format('d/m/Y') }}
                                @if($isExpired)
                                    <br><small class="badge bg-danger">Expired</small>
                                @endif
                            </span>
                        @else
                            -
                        @endif
                    </td>

                    <td>{{ $transaction->note ?? '-' }}</td>

                    <td>
                        {{-- Delete --}}
                        <form action="{{ route('admin.items.inventory.destroy', $transaction->id) }}"
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Delete this transaction? This will also adjust the stock balance.')"
                                    class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">No stock records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Summary Section --}}
        @if(count($sortedTransactions) > 0)
        <div class="mt-3 p-3 bg-light rounded">
            <h6>Current Stock Summary</h6>
            <div class="row">
                @foreach($itemBalances as $itemId => $balance)
                    @php
                        $item = $items->firstWhere('id', $itemId);
                    @endphp
                    @if($item)
                    <div class="col-md-3 mb-2">
                        <div class="card">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">{{ $item->name }}</h6>
                                <p class="card-text mb-0">
                                    <strong>Current Stock:</strong> {{ $balance }} Kg
                                </p>
                                @if($item->expiry_date)
                                    <small class="text-muted">
                                        Expires: {{ \Carbon\Carbon::parse($item->expiry_date)->format('d/m/Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>

<script>
    // Optional: Show/hide price fields based on transaction type
    document.addEventListener('DOMContentLoaded', function() {
        const entryTypeSelect = document.querySelector('select[name="entry_type_id"]');
        const retailPriceInput = document.querySelector('input[name="retail_price"]');
        const wholesalePriceInput = document.querySelector('input[name="whole_price"]');
        
        if (entryTypeSelect) {
            entryTypeSelect.addEventListener('change', function() {
                // If you want to disable price inputs for "out" transactions
                const selectedOption = this.options[this.selectedIndex];
                const optionText = selectedOption.text.toLowerCase();
                
                if (optionText.includes('out') || optionText.includes('damage')) {
                    retailPriceInput.disabled = true;
                    wholesalePriceInput.disabled = true;
                    retailPriceInput.value = '';
                    wholesalePriceInput.value = '';
                } else {
                    retailPriceInput.disabled = false;
                    wholesalePriceInput.disabled = false;
                }
            });
        }
    });
</script>
@endsection
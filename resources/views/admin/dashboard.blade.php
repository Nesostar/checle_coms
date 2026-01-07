@extends('layouts.admin')

@section('title', 'Dashboard - COMS')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Dashboard Metrics --}}
<div class="row dashboard-metrics">
    {{-- 2️⃣ Expenses Today --}}
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5>Tsh. {{ number_format($todayExpenses ?? 0, 2) }} /=</h5>
                <p>Today Total Expenses</p>
            </div>
        </div>
    </div>

    {{-- 1️⃣ Sales Today --}}
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5>Tsh. {{ number_format($todaySales ?? 0, 2) }} /=</h5>
                <p>Today Total Sales</p>
            </div>
        </div>
    </div>
    
    {{-- 3️⃣ Out of Stock Items --}}
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5>{{ $outOfStock ?? 0 }}</h5>
                <p>Out of Stock Items</p>
            </div>
        </div>
    </div>

    {{-- 4️⃣ Expired Items --}}
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5>{{ $expiredItems ?? 0 }}</h5>
                <p>Expired Items</p>
            </div>
        </div>
    </div>
</div>

{{-- Most Selling Products Table --}}
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Most Selling Products (Today)</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Qty Sold Today</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($topProducts ?? []) as $index => $p)
                        @php
                            $calculatedStock = $itemBalances[$p->item_id] ?? 0;
                            $stockBadge = $calculatedStock <= 0 ? 'badge bg-danger' : ($calculatedStock < 10 ? 'badge bg-warning text-dark' : 'badge bg-success');
                            $stockMessage = $calculatedStock <= 0 ? 'Out of Stock' : ($calculatedStock < 10 ? 'Low Stock' : 'In Stock');
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $p->item->name ?? 'N/A' }}</strong>
                                @if(isset($p->item))
                                    <br>
                                    <small class="text-muted">{{ $p->item->subcategory->name ?? 'N/A' }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $stockBadge }}">{{ $calculatedStock }} Kg</span>
                                <br>
                                <small class="text-muted">{{ $stockMessage }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $p->qty }} Kg</span>
                            </td>
                            <td>
                                <strong>Tsh. {{ number_format($p->total, 2) }}</strong>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                <i class="bi bi-cart-x me-2"></i>No sales yet today
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if(count($topProducts ?? []) > 0)
                <div class="mt-2 text-end">
                    <small class="text-muted">Showing top {{ count($topProducts) }} selling products</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Stock & Price Lookup --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Stock & Price Lookup</h6>
            </div>
            <div class="card-body">
                <select class="form-select mb-3" id="itemSearch">
                    <option value="" selected disabled>[ Select Item to View Details ]</option>
                    @foreach(($items ?? []) as $i)
                        @php
                            $calculatedStock = $itemBalances[$i->id] ?? 0;
                            $stockStatus = $calculatedStock <= 0 ? 'OUT OF STOCK' : ($calculatedStock < 10 ? 'LOW STOCK' : '');
                        @endphp
                        <option 
                            value="{{ $i->id }}"
                            data-qty="{{ $calculatedStock }}"
                            data-retail="{{ number_format($i->retail_price, 2) }}"
                            data-wholesale="{{ number_format($i->whole_price, 2) }}"
                            data-expiry="{{ $i->expiry_date ? \Carbon\Carbon::parse($i->expiry_date)->format('d/m/Y') : 'N/A' }}"
                            data-expiry-raw="{{ $i->expiry_date }}"
                            data-category="{{ htmlspecialchars($i->subcategory->category->name ?? 'N/A') }}"
                            data-subcategory="{{ htmlspecialchars($i->subcategory->name ?? 'N/A') }}"
                            data-stock-status="{{ $stockStatus }}"
                        >
                            {{ htmlspecialchars($i->name) }} ({{ $calculatedStock }} Kg)
                            @if($stockStatus)
                                - {{ $stockStatus }}
                            @endif
                        </option>
                    @endforeach
                </select>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr><th colspan="2" class="text-center">Item Details</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="40%"><strong>Current Stock</strong></td>
                                <td id="s_qty" class="text-end"><span class="badge bg-secondary">Select an item</span></td>
                            </tr>
                            <tr>
                                <td><strong>Retail Price</strong></td>
                                <td id="s_retail" class="text-end">-</td>
                            </tr>
                            <tr>
                                <td><strong>Wholesale Price</strong></td>
                                <td id="s_wholesale" class="text-end">-</td>
                            </tr>
                            <tr>
                                <td><strong>Expiry Date</strong></td>
                                <td id="s_expiry" class="text-end">-</td>
                            </tr>
                            <tr>
                                <td><strong>Category</strong></td>
                                <td id="s_category" class="text-end">-</td>
                            </tr>
                            <tr>
                                <td><strong>Subcategory</strong></td>
                                <td id="s_subcategory" class="text-end">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3" id="stock_guide">
                    <small>
                        <strong>Stock Status Guide:</strong><br>
                        <span class="badge bg-success">Green</span> = In Stock (10+ Kg)<br>
                        <span class="badge bg-warning text-dark">Yellow</span> = Low Stock (<10 Kg)<br>
                        <span class="badge bg-danger">Red</span> = Out of Stock (0 Kg)
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemSearch = document.getElementById('itemSearch');
    if(itemSearch){
        itemSearch.addEventListener('change', function(){
            const selected = this.options[this.selectedIndex];
            const qty = parseFloat(selected.dataset.qty || '0');
            const retail = selected.dataset.retail || '0.00';
            const wholesale = selected.dataset.wholesale || '0.00';
            const expiry = selected.dataset.expiry || 'N/A';
            const expiryRaw = selected.dataset.expiryRaw || '';
            const category = selected.dataset.category || 'N/A';
            const subcategory = selected.dataset.subcategory || 'N/A';

            // Quantity badge
            let qtyHtml = qty <= 0 ? `<span class="badge bg-danger">${qty} Kg (Out of Stock)</span>` :
                          qty < 10 ? `<span class="badge bg-warning text-dark">${qty} Kg (Low Stock)</span>` :
                          qty < 50 ? `<span class="badge bg-info">${qty} Kg</span>` :
                          `<span class="badge bg-success">${qty} Kg</span>`;

            document.getElementById('s_qty').innerHTML = qtyHtml;
            document.getElementById('s_retail').textContent = `Tsh. ${retail}`;
            document.getElementById('s_wholesale').textContent = `Tsh. ${wholesale}`;
            document.getElementById('s_category').textContent = category;
            document.getElementById('s_subcategory').textContent = subcategory;

            // Expiry badge
            if(expiryRaw){
                const expiryDate = new Date(expiryRaw);
                const today = new Date(); today.setHours(0,0,0,0);
                if(expiryDate < today){
                    document.getElementById('s_expiry').innerHTML = `<span class="badge bg-danger">${expiry} (Expired)</span>`;
                } else {
                    const diffDays = Math.ceil((expiryDate - today)/(1000*60*60*24));
                    if(diffDays <= 7){
                        document.getElementById('s_expiry').innerHTML = `<span class="badge bg-warning text-dark">${expiry} (Expires in ${diffDays} days)</span>`;
                    } else {
                        document.getElementById('s_expiry').textContent = expiry;
                    }
                }
            } else {
                document.getElementById('s_expiry').textContent = expiry;
            }
        });

        // Auto-select first item if only one exists
        if(itemSearch.options.length === 2){
            itemSearch.selectedIndex = 1;
            itemSearch.dispatchEvent(new Event('change'));
        }
    }
});
</script>

{{-- Styles --}}
<style>
.dashboard-metrics .card { transition: 0.2s; border:none; border-radius:10px; }
.dashboard-metrics .card:hover { transform: translateY(-5px); box-shadow:0 5px 15px rgba(0,0,0,0.1);}
#s_qty .badge { font-size:0.9em; padding:5px 10px;}
.table th { font-weight:600;}
.card-header { background-color:#f8f9fa; border-bottom:1px solid #dee2e6; font-weight:600;}
</style>

@endsection

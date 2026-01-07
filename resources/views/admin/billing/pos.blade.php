@extends('layouts.admin')

@section('title', 'POS - Point of Sales')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container-fluid">
    <h2 class="mb-3">POS — Point of Sales</h2>

    {{-- Alerts --}}
    <div id="alert-placeholder">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="row">

        {{-- Left: Select item & cart --}}
        <div class="col-md-7">

            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-cart-plus"></i> Add Item to Cart
                </div>
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Search Item</label>
                            <select id="selectItem" class="form-select">
                                <option value="">-- Select Item --</option>
                                @foreach($items as $item)
                                    @php
                                        // Use calculated stock from $itemBalances
                                        $currentStock = $itemBalances[$item->id] ?? $item->quantity;
                                        $stockClass = '';
                                        
                                        if ($currentStock <= 0) {
                                            $stockClass = 'text-danger';
                                        } elseif ($currentStock < 10) {
                                            $stockClass = 'text-warning';
                                        }
                                    @endphp
                                    <option value="{{ $item->id }}"
                                      data-retail="{{ $item->retail_price }}"
                                      data-wholesale="{{ $item->wholesale_price }}"
                                      data-stock="{{ $currentStock }}"
                                      data-name="{{ $item->name }}">
                                      {{ $item->name }} (Stock: {{ $currentStock }} Kg)
                                    </option>

                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
    <label class="form-label">Price Type</label>
    <select id="priceType" class="form-select">
        <option value="retail">Retail</option>
        <option value="wholesale">Wholesale</option>
    </select>
</div>

                        <div class="col-md-3">
                            <label class="form-label">Quantity (Kg)</label>
                            <input type="number" id="qty" class="form-control" 
                                   min="0.1" step="0.1" value="1" placeholder="e.g., 2.5">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button id="btnAdd" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-3" id="selectedItemInfo" style="display: none;">
                        <div class="alert alert-info py-2 mb-0">
                            <small>
                                <strong>Selected:</strong> 
                                <span id="selectedItemName"></span> | 
                                <span id="selectedItemPrice"></span> | 
                                <span id="selectedItemStock"></span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cart Table --}}
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cart"></i> Shopping Cart</span>
                    <span class="badge bg-info" id="cartCount">0 items</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Item Name</th>
                                    <th>Price (Tsh/Kg)</th>
                                    <th width="120">Qty (Kg)</th>
                                    <th>Subtotal (Tsh)</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr id="cartEmptyRow">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Your cart is empty</p>
                                        <small class="text-muted">Add items from the selection above</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer" id="cartFooter" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <button id="clearCartBtn" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> Clear Cart
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5 class="mb-0">Total: <span id="cartTotal" class="text-primary">Tsh. 0.00</span></h5>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Summary --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-receipt"></i> Order Summary</span>
                    <span id="summaryStatus" class="badge bg-light text-dark">Ready</span>
                </div>
                <div class="card-body">
                    <form id="posForm" action="{{ route('admin.billing.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Customer Name *</label>
                            <input type="text" name="customer_name" class="form-control" 
                                   placeholder="Enter customer name" value="Walk-in Customer" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Subtotal (Tsh)</label>
                                <input type="text" id="subtotal" class="form-control bg-light" readonly value="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Items in Cart</label>
                                <input type="text" id="itemCount" class="form-control bg-light" readonly value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount Paid (Tsh) *</label>
                            <div class="input-group">
                                <span class="input-group-text">Tsh.</span>
                                <input type="number" step="0.01" name="amount_paid"
                                       id="amountPaid" class="form-control" value="0" min="0" required>
                            </div>
                            <small class="text-muted">Enter the amount received from customer</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Balance (Tsh)</label>
                            <input type="text" id="balance" class="form-control bg-light" readonly value="0.00">
                        </div>

                        {{-- Hidden Inputs for cart items --}}
                        <div id="itemsInputs"></div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" id="completeSaleBtn" class="btn btn-primary btn-lg" disabled>
                                <i class="bi bi-credit-card"></i> Complete Sale
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- Quick Amount Buttons --}}
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <small class="text-muted"><i class="bi bi-lightning"></i> Quick Amount</small>
                </div>
                <div class="card-body py-2">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(5000)">5,000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(10000)">10,000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(20000)">20,000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(50000)">50,000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount('full')">Full</button>
                    </div>
                </div>
            </div>
            
            {{-- Stock Summary --}}
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <small class="text-muted"><i class="bi bi-box"></i> Stock Summary</small>
                </div>
                <div class="card-body py-2">
                    <small>
                        <span class="badge bg-success">In Stock (10+ Kg):</span> 
                        {{ $items->filter(fn($item) => ($itemBalances[$item->id] ?? $item->quantity) >= 10)->count() }} items<br>
                        <span class="badge bg-warning text-dark">Low Stock (<10 Kg):</span> 
                        {{ $items->filter(fn($item) => ($itemBalances[$item->id] ?? $item->quantity) < 10 && ($itemBalances[$item->id] ?? $item->quantity) > 0)->count() }} items<br>
                        <span class="badge bg-danger">Out of Stock:</span> 
                        {{ $items->filter(fn($item) => ($itemBalances[$item->id] ?? $item->quantity) <= 0)->count() }} items
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ================= FIXED JS ================= --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const selectItem = document.getElementById('selectItem');
    const qtyInput = document.getElementById('qty');
    const btnAdd = document.getElementById('btnAdd');
    const cartBody = document.getElementById('cartBody');
    const cartEmptyRow = document.getElementById('cartEmptyRow');
    const cartFooter = document.getElementById('cartFooter');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    const subtotalEl = document.getElementById('subtotal');
    const itemCountEl = document.getElementById('itemCount');
    const amountPaidEl = document.getElementById('amountPaid');
    const balanceEl = document.getElementById('balance');
    const itemsInputs = document.getElementById('itemsInputs');
    const completeSaleBtn = document.getElementById('completeSaleBtn');
    const clearCartBtn = document.getElementById('clearCartBtn');
    const selectedItemInfo = document.getElementById('selectedItemInfo');
    const selectedItemName = document.getElementById('selectedItemName');
    const selectedItemPrice = document.getElementById('selectedItemPrice');
    const selectedItemStock = document.getElementById('selectedItemStock');

    // Cart data - store items here
    let cart = [];

    // Format money
    function formatMoney(amount) {
        return 'Tsh. ' + parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Format number
    function formatNumber(num) {
        return parseFloat(num).toFixed(2);
    }

    // Show alert
    function showAlert(message, type = 'danger') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert:not(.alert-success):not(.alert-danger)');
        existingAlerts.forEach(alert => alert.remove());
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Find where to insert the alert
        const alertPlaceholder = document.getElementById('alert-placeholder');
        if (alertPlaceholder) {
            alertPlaceholder.appendChild(alertDiv);
        } else {
            // Fallback to top of container
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
        }
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Update cart UI
    function updateCartUI() {
        console.log('Updating cart UI, cart items:', cart); // Debug log
        
        // Clear existing rows and inputs
        cartBody.innerHTML = '';
        itemsInputs.innerHTML = '';
        
        if (cart.length === 0) {
            // Show empty cart message
            cartBody.appendChild(cartEmptyRow);
            cartFooter.style.display = 'none';
            cartCount.textContent = '0 items';
            cartTotal.textContent = 'Tsh. 0.00';
            subtotalEl.value = '0.00';
            itemCountEl.value = '0';
            balanceEl.value = '0.00';
            completeSaleBtn.disabled = true;
            document.getElementById('summaryStatus').className = 'badge bg-light text-dark';
            document.getElementById('summaryStatus').textContent = 'Empty';
            return;
        }
        
        // Hide empty row and show footer
        cartEmptyRow.style.display = 'none';
        cartFooter.style.display = 'block';
        completeSaleBtn.disabled = false;
        
        let subtotal = 0;
        let totalItems = 0;
        
        // Build cart rows
        cart.forEach((item, index) => {
            const lineTotal = item.price * item.qty;
            subtotal += lineTotal;
            totalItems += item.qty;
            
            console.log(`Item ${index}:`, item); // Debug log
            
            // Add row to cart table
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>${formatMoney(item.price)}</td>
                <td>${formatNumber(item.qty)} Kg</td>
                <td>${formatMoney(lineTotal)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}" title="Remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            cartBody.appendChild(row);
            
            // Add hidden inputs for form submission
            itemsInputs.innerHTML += `
    <input type="hidden" name="items[${index}][item_id]" value="${item.id}">
    <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
    <input type="hidden" name="items[${index}][price]" value="${item.price}">
    <input type="hidden" name="items[${index}][price_type]" value="${item.price_type}">
`;

        });
        
        // Update totals
        subtotalEl.value = formatNumber(subtotal);
        itemCountEl.value = formatNumber(totalItems);
        cartCount.textContent = `${cart.length} item${cart.length !== 1 ? 's' : ''}`;
        cartTotal.textContent = formatMoney(subtotal);
        
        // Update balance
        updateBalance();
        
        // Update status
        const statusElement = document.getElementById('summaryStatus');
        statusElement.className = 'badge bg-success';
        statusElement.textContent = 'Ready to Checkout';
        
        // Add event listeners to remove buttons
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                removeFromCart(index);
            });
        });
    }

    // Remove item from cart
    function removeFromCart(index) {
        if (confirm(`Remove "${cart[index].name}" from cart?`)) {
            const removedItem = cart.splice(index, 1)[0];
            showAlert(`Removed ${removedItem.name} from cart`, 'warning');
            updateCartUI();
        }
    }

    // Update balance
    function updateBalance() {
        const subtotal = parseFloat(subtotalEl.value) || 0;
        const amountPaid = parseFloat(amountPaidEl.value) || 0;
        const balance = subtotal - amountPaid;
        
        balanceEl.value = formatNumber(balance);
        
        // Color coding
        if (balance > 0) {
            balanceEl.className = 'form-control bg-light text-danger';
            balanceEl.title = `Customer owes: ${formatMoney(balance)}`;
        } else if (balance < 0) {
            balanceEl.className = 'form-control bg-light text-warning';
            balanceEl.title = `Change to give: ${formatMoney(Math.abs(balance))}`;
        } else {
            balanceEl.className = 'form-control bg-light text-success';
            balanceEl.title = 'Exact amount paid';
        }
    }

    // Add item to cart - FIXED VERSION
    btnAdd.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent form submission
        
        console.log('Add button clicked'); // Debug log
        
        // Validate selection
        if (!selectItem.value) {
            showAlert('Please select an item first');
            selectItem.focus();
            return;
        }
        
        const selectedOption = selectItem.options[selectItem.selectedIndex];
        console.log('Selected option:', selectedOption); // Debug log
        
        const itemId = selectItem.value;
        const itemName = selectedOption.getAttribute('data-name');
        const priceType = document.getElementById('priceType').value;

const itemPrice = priceType === 'wholesale'
    ? parseFloat(selectedOption.getAttribute('data-wholesale'))
    : parseFloat(selectedOption.getAttribute('data-retail'));

        const availableStock = parseFloat(selectedOption.getAttribute('data-stock'));
        let quantity = parseFloat(qtyInput.value);
        
        console.log('Parsed data:', { itemId, itemName, itemPrice, availableStock, quantity }); // Debug log
        
        // Validate quantity
        if (isNaN(quantity) || quantity <= 0) {
            showAlert('Please enter a valid quantity (minimum 0.1 Kg)');
            qtyInput.focus();
            return;
        }
        
        if (quantity > 999) {
            showAlert('Maximum quantity per item is 999 Kg');
            return;
        }
        
        // Check stock availability
        if (quantity > availableStock) {
            showAlert(`Insufficient stock! Only ${availableStock} Kg available for ${itemName}`);
            return;
        }
        
        // Check if item already in cart
        const existingItemIndex = cart.findIndex(item => item.id === itemId);
        
        if (existingItemIndex !== -1) {
            // Update existing item quantity
            const newTotalQty = cart[existingItemIndex].qty + quantity;
            
            if (newTotalQty > availableStock) {
                showAlert(`Cannot add more. Total quantity (${newTotalQty} Kg) would exceed available stock (${availableStock} Kg)`);
                return;
            }
            
            cart[existingItemIndex].qty = newTotalQty;
            showAlert(`Updated ${itemName} quantity to ${formatNumber(newTotalQty)} Kg`, 'info');
        } else {
            // Add new item
            cart.push({
    id: itemId,
    name: itemName,
    price: itemPrice,
    qty: quantity,
    price_type: priceType
});

            showAlert(`Added ${itemName} (${formatNumber(quantity)} Kg) to cart`, 'success');
        }
        
        // Update UI
        updateCartUI();
        
        // Reset selection
        selectItem.selectedIndex = 0;
        qtyInput.value = 1;
        selectedItemInfo.style.display = 'none';
    });

    // Clear cart
    clearCartBtn.addEventListener('click', function() {
        if (cart.length === 0) {
            showAlert('Cart is already empty');
            return;
        }
        
        if (confirm(`Clear all ${cart.length} items from cart?`)) {
            cart = [];
            updateCartUI();
            showAlert('Cart cleared successfully', 'warning');
        }
    });

    // Update selected item info
    selectItem.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            selectedItemName.textContent = selectedOption.getAttribute('data-name');
            const priceType = document.getElementById('priceType').value;
const price = priceType === 'wholesale'
    ? selectedOption.getAttribute('data-wholesale')
    : selectedOption.getAttribute('data-retail');

selectedItemPrice.textContent = formatMoney(price);

            selectedItemStock.textContent = `Stock: ${selectedOption.getAttribute('data-stock')} Kg`;
            selectedItemInfo.style.display = 'block';
            
            // Auto-focus quantity input
            qtyInput.focus();
            qtyInput.select();
        } else {
            selectedItemInfo.style.display = 'none';
        }
    });

    // Update balance when amount paid changes
    amountPaidEl.addEventListener('input', updateBalance);
    
    // Quick amount buttons
    window.setAmount = function(amount) {
        if (amount === 'full') {
            amountPaidEl.value = parseFloat(subtotalEl.value) || 0;
        } else {
            amountPaidEl.value = amount;
        }
        updateBalance();
        amountPaidEl.focus();
    };

    // Form submission validation
    document.getElementById('posForm').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            showAlert('Cannot complete sale with empty cart!');
            return;
        }
        
        const subtotal = parseFloat(subtotalEl.value) || 0;
        const amountPaid = parseFloat(amountPaidEl.value) || 0;
        
        if (amountPaid < 0) {
            e.preventDefault();
            showAlert('Amount paid cannot be negative');
            return;
        }
        
        // Show confirmation
        if (!confirm(`Complete sale for ${formatMoney(subtotal)}?\nAmount Paid: ${formatMoney(amountPaid)}`)) {
            e.preventDefault();
        }
    });

    // Allow Enter key in quantity field to add item
    qtyInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnAdd.click();
        }
    });

    // Initialize
    updateCartUI();
});
</script>
@endpush

<style>
.table td, .table th {
    vertical-align: middle;
}
#cartBody tr:hover {
    background-color: #f8f9fa;
}
.btn-sm {
    padding: 0.25rem 0.5rem;
}
.badge {
    font-size: 0.75em;
}
.input-group-text {
    background-color: #f8f9fa;
}
</style>
@endsection
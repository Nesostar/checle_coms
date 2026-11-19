
@extends('layouts.app')

@section('title', 'Manage Quotations - Cashier COMS')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">Manage Quotations</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ADD / EDIT QUOTATION -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong id="form-title">Add Quotation</strong>
        </div>

        <div class="card-body">

            <form id="quotation-form"
                  action="{{ route('cashier.quotations.store') }}"
                  method="POST">

                @csrf

                <input type="hidden" name="id" id="quotation_id">

                <!-- QUOTATION DETAILS -->
                <div class="row g-3 mb-4">

                    <div class="col-md-4">
                        <label class="form-label">Customer</label>

                        <select name="customer_id"
                                id="customer_id"
                                class="form-control"
                                required>
                            <option value="">Select Customer</option>

                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Quotation Date</label>

                        <input type="date"
                               name="quotation_date"
                               id="quotation_date"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>

                        <select name="status"
                                id="status"
                                class="form-control"
                                required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                </div>

                <!-- QUOTATION ITEMS -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Quotation Items</h5>

                    <button type="button"
                            class="btn btn-success btn-sm"
                            id="add-item-btn">
                        + Add Item
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle"
                           id="items-table">

                        <thead class="table-light">
                            <tr>
                                <th style="min-width:200px;">Product / Item</th>
                                <th style="min-width:130px;">Quantity (KG)</th>
                                <th style="min-width:160px;">Price per KG (TZS)</th>
                                <th style="min-width:160px;">Total (TZS)</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>

                        <tbody id="items-container">
                        </tbody>

                    </table>
                </div>

                <!-- GRAND TOTAL -->
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            Grand Total (TZS)
                        </label>

                        <input type="text"
                               id="grand-total"
                               class="form-control fw-bold"
                               value="0.00"
                               readonly>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        Save Quotation
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            id="clear-btn">
                        Clear Form
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- QUOTATIONS LIST -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>Quotations List</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Total (TZS)</th>
                            <th width="160">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($quotations as $q)

                            <tr>
                                <td>{{ $q->id }}</td>

                                <td>{{ $q->customer->name ?? 'N/A' }}</td>

                                <td>{{ $q->quotation_date }}</td>

                                <td>
                                    @forelse($q->items as $item)
                                        <div>
                                            {{ $item->item_name }}
                                            ({{ number_format($item->quantity) }} KG)
                                        </div>
                                    @empty
                                        <span class="text-muted">No items</span>
                                    @endforelse
                                </td>

                                <td>
                                    <span class="badge
                                        {{ $q->status === 'Approved' ? 'bg-success' :
                                           ($q->status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ $q->status }}
                                    </span>
                                </td>

                                <td class="fw-bold">
                                    {{ number_format($q->total, 2) }}
                                </td>

                                <td>
                                    <button type="button"
                                            class="btn btn-info btn-sm edit-btn"
                                            data-id="{{ $q->id }}"
                                            data-customer="{{ $q->customer_id }}"
                                            data-date="{{ $q->quotation_date }}"
                                            data-status="{{ $q->status }}"
                                            data-items="{{ $q->items->toJson() }}">
                                        Edit
                                    </button>

                                    <form action="{{ route('cashier.quotations.destroy', $q->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this quotation?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No quotations found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('quotation-form');
    const itemsContainer = document.getElementById('items-container');
    const grandTotal = document.getElementById('grand-total');

    const quotationId = document.getElementById('quotation_id');
    const customerId = document.getElementById('customer_id');
    const quotationDate = document.getElementById('quotation_date');
    const status = document.getElementById('status');

    const formTitle = document.getElementById('form-title');
    const saveBtn = document.getElementById('save-btn');

    let itemIndex = 0;

    // FORMAT CURRENCY
    function formatMoney(amount) {
        return Number(amount || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ADD ITEM ROW
    function addItemRow(item = {}) {

        const index = itemIndex++;

        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                <input type="text"
                       name="items[${index}][item_name]"
                       class="form-control item-name"
                       placeholder="Enter product name"
                       required>
            </td>

            <td>
                <input type="number"
                       name="items[${index}][quantity]"
                       class="form-control item-quantity"
                       placeholder="KG"
                       min="1"
                       step="1"
                       required>
            </td>

            <td>
                <input type="number"
                       name="items[${index}][unit_price]"
                       class="form-control item-price"
                       placeholder="Price per KG"
                       min="0"
                       step="0.01"
                       required>
            </td>

            <td>
                <input type="text"
                       class="form-control item-total"
                       value="0.00"
                       readonly>
            </td>

            <td>
                <button type="button"
                        class="btn btn-danger btn-sm remove-item">
                    Remove
                </button>
            </td>
        `;

        itemsContainer.appendChild(row);

        row.querySelector('.item-name').value = item.item_name || '';
        row.querySelector('.item-quantity').value = item.quantity || '';
        row.querySelector('.item-price').value = item.unit_price || '';

        calculateTotals();
    }

    // CALCULATE ITEM TOTALS AND GRAND TOTAL
    function calculateTotals() {

        let total = 0;

        document.querySelectorAll('#items-container tr').forEach(row => {

            const quantity = Number(
                row.querySelector('.item-quantity').value
            ) || 0;

            const price = Number(
                row.querySelector('.item-price').value
            ) || 0;

            const itemTotal = quantity * price;

            row.querySelector('.item-total').value = formatMoney(itemTotal);

            total += itemTotal;
        });

        grandTotal.value = formatMoney(total);
    }

    // ADD FIRST ITEM
    addItemRow();

    // ADD ITEM BUTTON
    document.getElementById('add-item-btn').addEventListener('click', function () {
        addItemRow();
    });

    // CALCULATE WHEN QUANTITY OR PRICE CHANGES
    itemsContainer.addEventListener('input', function (event) {

        if (
            event.target.classList.contains('item-quantity') ||
            event.target.classList.contains('item-price')
        ) {
            calculateTotals();
        }
    });

    // REMOVE ITEM
    itemsContainer.addEventListener('click', function (event) {

        if (event.target.classList.contains('remove-item')) {

            if (itemsContainer.querySelectorAll('tr').length <= 1) {
                alert('A quotation must have at least one item.');
                return;
            }

            event.target.closest('tr').remove();
            calculateTotals();
        }
    });

    // EDIT QUOTATION
    document.querySelectorAll('.edit-btn').forEach(button => {

        button.addEventListener('click', function () {

            quotationId.value = this.dataset.id;
            customerId.value = this.dataset.customer;
            quotationDate.value = this.dataset.date;
            status.value = this.dataset.status;

            itemsContainer.innerHTML = '';
            itemIndex = 0;

            let items = [];

            try {
                items = JSON.parse(this.dataset.items || '[]');
            } catch (error) {
                console.error('Unable to load quotation items:', error);
            }

            if (items.length > 0) {
                items.forEach(item => addItemRow(item));
            } else {
                addItemRow();
            }

            calculateTotals();

            formTitle.textContent = 'Edit Quotation #' + this.dataset.id;
            saveBtn.textContent = 'Update Quotation';

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });

    // CLEAR / RESET FORM
    document.getElementById('clear-btn').addEventListener('click', function () {

        form.reset();

        quotationId.value = '';
        quotationDate.value = '{{ date('Y-m-d') }}';

        itemsContainer.innerHTML = '';
        itemIndex = 0;

        addItemRow();

        grandTotal.value = '0.00';

        formTitle.textContent = 'Add Quotation';
        saveBtn.textContent = 'Save Quotation';
    });

});
</script>

@endsection

@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-3">
    <h4>Invoice Management</h4>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Add / Edit Invoice -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Add / Edit Invoice</div>
        <div class="card-body">
            <form method="POST" action="{{ route('cashier.invoices.store') }}" id="invoiceForm">
                @csrf
                <input type="hidden" name="id">

                <!-- Header Section -->
                <div class="row mb-3">
                    <div class="col-md-4 col-12 mb-2">
                        <select name="customer_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-6 mb-2">
                        <input type="date" name="invoice_date" class="form-control" required>
                    </div>

                    <div class="col-md-3 col-6 mb-2">
                        <input type="date" name="due_date" class="form-control" required>
                    </div>

                    <div class="col-md-2 col-12 mb-2">
                        <select name="status" class="form-control">
                            <option value="Draft">Draft</option>
                            <option value="Sent">Sent</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive">
                    <table class="table table-bordered mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th width="80">Qty</th>
                                <th width="120">Price Type</th>
                                <th width="120">Price</th>
                                <th width="120">Subtotal</th>
                                <th width="50">×</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr>
                                <td>
                                    <select class="form-control item">
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" class="form-control qty" value="1" min="1"></td>
                                <td>
                                    <select class="form-select price_type">
                                        <option value="retail">Retail</option>
                                        <option value="wholesale">Wholesale</option>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control price" min="0" step="0.01"></td>
                                <td class="subtotal text-end">0.00</td>
                                <td><button type="button" class="btn btn-danger btn-sm remove">×</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-secondary btn-sm mb-3" id="addRow">+ Add Item</button>

                <h5 class="text-end mt-3 mb-3">
                    Total: Tsh <span id="grandTotal">0.00</span>
                    <input type="hidden" name="total" id="totalInput" required>
                </h5>

                <button class="btn btn-success">Save Invoice</button>
            </form>
        </div>
    </div>

    <!-- Invoice List -->
    <div class="card">
        <div class="card-header bg-dark text-white">Invoices List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $inv)
                            <tr>
                                <td>{{ $inv->id }}</td>
                                <td>{{ $inv->customer->name ?? 'N/A' }}</td>
                                <td>{{ $inv->invoice_date }}</td>
                                <td>{{ $inv->due_date }}</td>
                                <td>
                                    <span class="badge bg-{{ $inv->status == 'Paid' ? 'success' : ($inv->status == 'Sent' ? 'warning' : 'secondary') }}">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($inv->total, 2) }}</td>
                                <td>
                                    @if($inv->items->count())
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#invItems{{ $inv->id }}">View</button>

                                        <!-- Items Modal -->
                                        <div class="modal fade" id="invItems{{ $inv->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Invoice #{{ $inv->id }} Items</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Qty</th>
                                                                        <th>Price</th>
                                                                        <th>Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($inv->items as $item)
                                                                        <tr>
                                                                            <td>{{ optional($item->item)->name ?? 'N/A' }}</td>
                                                                            <td>{{ $item->qty }}</td>
                                                                            <td>{{ number_format($item->price,2) }}</td>
                                                                            <td>{{ number_format($item->subtotal,2) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-primary edit-btn"
                                        data-id="{{ $inv->id }}"
                                        data-customer="{{ $inv->customer_id }}"
                                        data-invoice="{{ $inv->invoice_date }}"
                                        data-due="{{ $inv->due_date }}"
                                        data-status="{{ $inv->status }}"
                                        data-total="{{ $inv->total }}">
                                        Edit
                                    </button>

                                    <!-- PDF -->
                                    <a href="{{ route('cashier.invoices.pdf', $inv->id) }}" class="btn btn-success btn-sm">PDF</a>

                                    <!-- Delete -->
                                    <form action="{{ route('cashier.invoices.destroy', $inv->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete invoice?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function recalc() {
    let total = 0;
    document.querySelectorAll('#itemsBody tr').forEach((row, index) => {
        row.querySelector('.item').name = `items[${index}][item_id]`;
        row.querySelector('.qty').name = `items[${index}][qty]`;
        row.querySelector('.price_type').name = `items[${index}][price_type]`;
        row.querySelector('.price').name = `items[${index}][price]`;

        let qty = parseFloat(row.querySelector('.qty').value) || 0;
        let price = parseFloat(row.querySelector('.price').value) || 0;
        let subtotal = qty * price;

        row.querySelector('.subtotal').innerText = subtotal.toFixed(2);
        total += subtotal;
    });

    document.getElementById('grandTotal').innerText = total.toFixed(2);
    document.getElementById('totalInput').value = total.toFixed(2);
}

document.getElementById('addRow').onclick = () => {
    let row = document.querySelector('#itemsBody tr').cloneNode(true);
    row.querySelectorAll('input').forEach(input => input.value = '');
    row.querySelector('.qty').value = 1;
    row.querySelector('.subtotal').innerText = '0.00';
    document.getElementById('itemsBody').appendChild(row);
    recalc();
};

document.addEventListener('click', function(e) {
    if(e.target.classList.contains('remove')) {
        if(document.querySelectorAll('#itemsBody tr').length > 1) {
            e.target.closest('tr').remove();
            recalc();
        } else {
            alert('At least one item is required');
        }
    }
});

document.addEventListener('input', recalc);

// Edit invoice functionality
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-btn');
    const form = document.getElementById('invoiceForm');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.querySelector('input[name="id"]').value = this.dataset.id;
            document.querySelector('select[name="customer_id"]').value = this.dataset.customer;
            document.querySelector('input[name="invoice_date"]').value = this.dataset.invoice;
            document.querySelector('input[name="due_date"]').value = this.dataset.due;
            document.querySelector('select[name="status"]').value = this.dataset.status;
            document.querySelector('input[name="total"]').value = this.dataset.total;

            document.querySelector('.card-header.bg-primary').scrollIntoView({ behavior: 'smooth' });

            const saveButton = form.querySelector('button[type="submit"]');
            saveButton.textContent = 'Update Invoice';
            saveButton.className = 'btn btn-warning';
        });
    });

    recalc();
});
</script>

@endsection

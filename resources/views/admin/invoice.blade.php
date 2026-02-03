@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-3">

    <h4>Invoice Management</h4>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Add Invoice Form -->
    <form method="POST" action="{{ route('admin.invoices.store') }}">
        @csrf

        {{-- ================= HEADER ================= --}}
        <div class="row mb-3">
            <div class="col-md-4 col-12 mb-2">
                <select name="customer_id" class="form-control" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-6 mb-2">
                <input type="date" name="invoice_date" class="form-control" required>
            </div>

            <div class="col-md-3 col-6 mb-2">
                <input type="date" name="due_date" class="form-control" required>
            </div>

            <div class="col-md-2 col-6 mb-2">
                <select name="status" class="form-control">
                    <option value="Draft">Draft</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
        </div>

        {{-- ================= ITEMS TABLE ================= --}}
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price Type</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>×</th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr>
                        <td>
                            <select class="form-control item">
                                @foreach($items as $i)
                                    <option value="{{ $i->id }}">{{ $i->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control qty" value="1" min="1">
                        </td>
                        <td>
                            <select class="form-control price_type">
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control price" min="0">
                        </td>
                        <td class="subtotal text-end">0.00</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove">×</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-secondary btn-sm mb-3" id="addRow">+ Add Item</button>

        <h5 class="text-end mt-3">
            Total: Tsh <span id="grandTotal">0.00</span>
        </h5>

        <button class="btn btn-success mt-2">Save Invoice</button>
    </form>

    <hr>

    {{-- ================= INVOICE LIST ================= --}}
    <h5>Invoices List</h5>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                    <tr>
                        <td>{{ $inv->id }}</td>
                        <td>{{ $inv->customer->name ?? 'N/A' }}</td>
                        <td>{{ $inv->invoice_date }}</td>
                        <td>{{ number_format($inv->total,2) }}</td>
                        <td class="d-flex flex-wrap gap-1">
                            <!-- PDF Button -->
                            <a href="{{ route('admin.invoices.pdf', $inv->id) }}" class="btn btn-success btn-sm" target="_blank">
                                PDF
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('admin.invoices.delete', $inv->id) }}"
                                  method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirm('Delete invoice?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- ================= JAVASCRIPT ================= --}}
<script>
function recalc() {
    let total = 0;

    document.querySelectorAll('#itemsBody tr').forEach((row, index) => {
        row.querySelector('.item').name       = `items[${index}][item_id]`;
        row.querySelector('.qty').name        = `items[${index}][qty]`;
        row.querySelector('.price').name      = `items[${index}][price]`;
        row.querySelector('.price_type').name = `items[${index}][price_type]`;

        let qty   = parseFloat(row.querySelector('.qty').value)   || 0;
        let price = parseFloat(row.querySelector('.price').value) || 0;
        let sub = qty * price;
        row.querySelector('.subtotal').innerText = sub.toFixed(2);
        total += sub;
    });

    document.getElementById('grandTotal').innerText = total.toFixed(2);
}

document.getElementById('addRow').onclick = () => {
    let row = document.querySelector('#itemsBody tr').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    row.querySelector('.price_type').value = 'retail';
    row.querySelector('.qty').value = 1;
    row.querySelector('.subtotal').innerText = '0.00';
    document.getElementById('itemsBody').appendChild(row);
    recalc();
};

document.addEventListener('input', recalc);

document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove')){
        if(document.querySelectorAll('#itemsBody tr').length > 1){
            e.target.closest('tr').remove();
            recalc();
        } else {
            alert('At least one item is required.');
        }
    }
});
</script>

@endsection

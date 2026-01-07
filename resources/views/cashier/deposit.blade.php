@extends('layouts.app')

@section('title', 'Deposit - Cashier COMS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    <h2 class="mb-4">Cashier Deposit</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Deposit Form -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            Add New Deposit
        </div>
        <div class="card-body">

            <form action="{{ route('cashier.deposit.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label><strong>Depositor Name</strong></label>
                        <input type="text" name="depositor_name" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label><strong>Amount</strong></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label><strong>Payment Method</strong></label>
                        <select name="payment_method" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Mobile Money">Mobile Money</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label><strong>Deposit Date</strong></label>
                        <input type="date" name="deposit_date" class="form-control" required>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label><strong>Description (Optional)</strong></label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                </div>

                <button class="btn btn-success">Save Deposit</button>
            </form>

        </div>
    </div>

    <!-- Deposit List -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            Deposit History
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Depositor</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Description</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($deposits as $deposit)
                        <tr>
                            <td>{{ $deposit->id }}</td>
                            <td>{{ $deposit->depositor_name }}</td>
                            <td>{{ number_format($deposit->amount, 2) }}</td>
                            <td>{{ $deposit->payment_method }}</td>
                            <td>{{ $deposit->deposit_date }}</td>
                            <td>{{ $deposit->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No deposits recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection

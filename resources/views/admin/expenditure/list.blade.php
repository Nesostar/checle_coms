@extends('layouts.admin')
@section('title','Expenditure List')

@section('content')
<div class="container">
    <h4>Expenditure List</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-header bg-primary text-white">Add Expenditure</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.expenditure.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input name="title" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="col-md-2">
                        <input name="amount" type="number" step="0.01" class="form-control" placeholder="Amount" required>
                    </div>
                    <div class="col-md-2">
                        <input name="spent_on" type="date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success w-100">Add</button>
                    </div>
                </div>
                <textarea name="description" class="form-control mt-2" placeholder="Note"></textarea>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th><th>Title</th><th>Category</th>
                <th>Amount</th><th>Date</th><th>Note</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenditures as $exp)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $exp->title }}</td>
                <td>{{ $exp->category->name }}</td>
                <td>{{ number_format($exp->amount,2) }}</td>
                <td>{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                <td>{{ $exp->description ?? '-' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.expenditure.update',$exp->id) }}" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="category_id" value="{{ $exp->category_id }}">
                        <input type="hidden" name="amount" value="{{ $exp->amount }}">
                        <input type="hidden" name="description" value="{{ $exp->description }}">
                        <input type="hidden" name="date" value="{{ $exp->date }}">
                        <button class="btn btn-warning btn-sm"
                            onclick="event.preventDefault();
                            let a=prompt('Amount','{{ $exp->amount }}');
                            let d=prompt('Date','{{ $exp->date }}');
                            let n=prompt('Note','{{ $exp->description }}');
                            if(a){this.form.amount.value=a;this.form.date.value=d;this.form.description.value=n;this.form.submit();}">
                            Edit
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.expenditure.destroy',$exp->id) }}" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete expenditure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No expenditures found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

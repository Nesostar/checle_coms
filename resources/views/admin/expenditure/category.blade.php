@extends('layouts.admin')
@section('title','Expenditure Category')

@section('content')
<div class="container">
    <h4>Expenditure Categories</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.expenditure.category.store') }}" class="row g-2 mb-3">
        @csrf
        <div class="col-md-4">
            <input name="name" class="form-control" placeholder="Category Name" required>
        </div>
        <div class="col-md-6">
            <input name="description" class="form-control" placeholder="Description">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Add</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>#</th><th>Name</th><th>Description</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->description ?? '-' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.expenditure.category.update',$cat->id) }}" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="name" value="{{ $cat->name }}">
                        <input type="hidden" name="description" value="{{ $cat->description }}">
                        <button class="btn btn-warning btn-sm"
                            onclick="event.preventDefault();
                            let n=prompt('Name','{{ $cat->name }}');
                            let d=prompt('Description','{{ $cat->description }}');
                            if(n){this.form.name.value=n;this.form.description.value=d;this.form.submit();}">
                            Edit
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.expenditure.category.destroy',$cat->id) }}" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete this category?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Item Name')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    <h2>Item Name</h2>
    <p>Manage individual item names here.</p>

    {{-- Add Item Name Form --}}
    <div class="card mt-3 p-3">
    <form action="{{ route('cashier.items.names.store') }}" method="POST">
            @csrf
            <div class="form-group mb-2">
                <label for="subcategory_id">Select Subcategory</label>
                <select name="subcategory_id" class="form-control" required>
                    <option value="">-- Select Subcategory --</option>
                    @foreach($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }} ({{ $subcategory->category->name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-2">
                <label for="name">Item Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Enter item name">
            </div>

            <button type="submit" class="btn btn-primary mt-2">Add Item</button>
        </form>
    </div>


    {{-- Items Table --}}
    <div class="card mt-4 p-3">
        <h4>Item List</h4>

        <table class="table table-bordered mt-2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Subcategory</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->subcategory->name }}</td>
                    <td>{{ $item->subcategory->category->name }}</td>

                    <td>

                        {{-- Edit Button triggers Modal --}}
                        <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editItemModal{{ $item->id }}">
                            Edit
                        </button>

                        {{-- Delete Button --}}
                        <form action="{{ route('cashier.items.names.destroy', $item->id) }}"
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Delete this item?')"
                                    class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </form>

                    </td>
                </tr>

                {{-- Edit Modal --}}
                <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form action="{{ route('cashier.items.names.update', $item->id) }}"
                                  method="POST">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group mb-2">
                                        <label>Subcategory</label>
                                        <select name="subcategory_id"
                                                class="form-control" required>
                                            @foreach($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}"
                                                    {{ $subcategory->id == $item->subcategory_id ? 'selected' : '' }}>
                                                    {{ $subcategory->name }} ({{ $subcategory->category->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label>Item Name</label>
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               value="{{ $item->name }}"
                                               required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button class="btn btn-success">Update</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection

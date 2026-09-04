@extends('layouts.app')
@section('title', 'Entry Type')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

    <h2>Entry Type</h2>
    <p>Define item entry types (purchase, restock, etc.) here.</p>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Add Entry Type Form --}}
    <div class="card p-3 mt-3">
        <form action="{{ route('cashier.items.entrytype.store') }}" method="POST">
            @csrf
            <div class="form-group mb-2">
    <label>Item</label>
    <select name="item_id" class="form-select" required>
        <option value="">Select Item</option>

        @foreach($items as $item)
            <option value="{{ $item->id }}">
                {{ $item->name }}
            </option>
        @endforeach
    </select>
</div>

            <div class="form-group mb-2">
                <label>Entry Type Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g Purchase" required>
            </div>

            <div class="form-group mb-2">
                <label>Direction</label>
                <select name="direction" class="form-select" required>
                    <option value="in">IN (Purchase / Stock In)</option>
                    <option value="out">OUT (Sales)</option>
                    <option value="damage">DAMAGE</option>
                    <option value="adjustment">ADJUSTMENT</option>
                </select>
            </div>

            <div class="form-group mb-2">
                <label>Description (optional)</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <button class="btn btn-primary mt-2">Add Entry Type</button>
        </form>
    </div>

    {{-- Entry Type Table --}}
    <div class="card p-3 mt-4">
        <h4>Entry Type List</h4>

        <table class="table table-bordered mt-3">
        <thead>
            <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Name</th>
            <th>Direction</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>

            <tbody>
                @foreach($entryTypes as $type)
                <tr>
    <td>{{ $type->id }}</td>
    <td>{{ $type->item->name ?? '—' }}</td>
    <td>{{ $type->name }}</td>
    <td>{{ strtoupper($type->direction) }}</td>
    <td>{{ $type->description ?? '—' }}</td>
    <td>
                        {{-- Edit Button --}}
                        <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editEntryType{{ $type->id }}">
                            Edit
                        </button>

                        {{-- Delete --}}
                        <form action="{{ route('cashier.items.entrytype.destroy', $type->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this entry type?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- Edit Modal --}}
                <div class="modal fade" id="editEntryType{{ $type->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('cashier.items.entrytype.update', $type->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Entry Type</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group mb-2">
                                        <label>Entry Type Name</label>
                                        <input type="text" name="name" value="{{ $type->name }}" class="form-control" required>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label>Item</label>
                                        <select name="item_id" class="form-select" required>
                                            <option value="">Select Item</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" {{ $type->item_id == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label>Direction</label>
                                        <select name="direction" class="form-select" required>
                                            <option value="in" {{ $type->direction === 'in' ? 'selected' : '' }}>IN (Purchase / Stock In)</option>
                                            <option value="out" {{ $type->direction === 'out' ? 'selected' : '' }}>OUT (Sales)</option>
                                            <option value="damage" {{ $type->direction === 'damage' ? 'selected' : '' }}>DAMAGE</option>
                                            <option value="adjustment" {{ $type->direction === 'adjustment' ? 'selected' : '' }}>ADJUSTMENT</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control">{{ $type->description }}</textarea>
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

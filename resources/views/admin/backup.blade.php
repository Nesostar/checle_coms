@extends('layouts.admin')

@section('title', 'Database Backup - Admin COMS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<h2>Database Backup</h2>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('admin.backup.run') }}" class="btn btn-primary mb-3">
    Create New Backup
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Backup File</th>
            <th>Action</th>
        </tr>
    </thead>
    @stack('scripts')
    <tbody>
        @forelse($files as $file)
        <tr>
            <td>{{ basename($file) }}</td>
            <td>
                <a href="{{ route('admin.backup.download', basename($file)) }}" class="btn btn-success btn-sm">
                    Download
                </a>

                <form action="{{ route('admin.backup.delete', basename($file)) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this backup?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="2">No backups found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection

@extends('layouts.app')

@section('title', 'Logout - COMS')

@section('content')
<h2>Logout</h2>
<p>Are you sure you want to log out from the system?</p>

<form id="logout-form" action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-danger">Logout</button>
</form>
@endsection

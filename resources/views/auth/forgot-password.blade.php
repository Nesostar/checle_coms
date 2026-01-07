@extends('layouts.password')

@section('title', 'Forgot Password - COMS')
@section('header', 'Forgot Password')

@section('content')

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-3">
        <label>Email Address</label>
        <input type="email" name="email" class="form-control" required>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Send Reset Link
    </button>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}">Back to Login</a>
    </div>
</form>

@endsection

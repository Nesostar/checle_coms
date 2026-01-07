@extends('layouts.password')

@section('title', 'Reset Password - COMS')
@section('header', 'Reset Password')

@section('content')

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ request()->email }}">

    <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="password" class="form-control" required>
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Reset Password
    </button>
</form>
@endsection
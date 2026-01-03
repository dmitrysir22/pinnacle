@extends('layouts.guest')

@section('title', 'Agent Login')

@section('content')
<h3 class="mb-4 text-center">Agent Login</h3>

<form method="POST" action="{{ url('/login') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button class="btn btn-primary w-100">Login</button>
</form>

<div class="text-center mt-3">
    <a href="{{ url('/register') }}">Request agent access</a>
</div>
@endsection

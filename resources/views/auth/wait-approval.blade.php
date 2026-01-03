@extends('layouts.guest')

@section('content')
<div class="container mt-5" style="max-width:420px;">
    <h4 class="mb-3 text-center">Email Verified!</h4>
    <p class="text-center">
        Your email has been successfully verified.
    </p>
    <p class="text-center">
        Your account is pending approval by an administrator.
    </p>
    <p class="text-center text-muted">
        You will receive an email once your account has been approved.
    </p>
    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="btn btn-primary">Back to Login</a>
    </div>
</div>
@endsection

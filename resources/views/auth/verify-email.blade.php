@extends('layouts.guest')

@section('content')
<div class="container mt-5" style="max-width:420px;">
    <h4 class="mb-3 text-center">Almost there!</h4>
    <p class="text-center">
        Thank you for registering. A verification email has been sent to <strong>{{ $email }}</strong>.
    </p>
    <p class="text-center">
        Please check your inbox (and spam folder) and click the verification link to activate your account.
    </p>
    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>
    </div>
</div>
@endsection

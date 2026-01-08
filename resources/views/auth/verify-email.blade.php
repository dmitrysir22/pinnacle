@extends('layouts.guest')

@section('title', 'Verify Email | Pinnacle International Freight')

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-header">
            <img src="/agent/assets/logo.png" class="toplogo" alt="PIF Portal Logo">

            <h1 class="auth-title">Almost there!</h1>

       
			
        </div>
		
		
		   <div class="auth-body text-center">
            <p class="mb-4">
                Thank you for registering. A verification email has been sent to
                <strong>{{ $email }}</strong>.
            </p>

        </div>
		

        <div class="auth-body text-center">
            <p class="mb-4">
                Please check your inbox (and spam folder) and click the verification
                link to activate your account.
            </p>

<p class="mb-4">
                Once activated, a member of the Pinnacle team will need to approve your account. Once approved you will receive a further follow up email. 
            </p>




            <a href="{{ route('login') }}"
               class="btn btn-primary w-100 auth-btn">
                Go to Login
            </a>
        </div>
    </div>

    <div class="auth-legal">
        © 2026 Pinnacle International Freight Limited — Unit 1 Swannington Road,
        Broughton Astley, Leicester, LE9 6TU, UK. All rights reserved.
    </div>
</div>
@endsection

@push('scripts')
    @include('layouts.agent.scripts')
@endpush

@extends('layouts.guest')

@section('title', 'Agent Registration | Pinnacle International Freight')

@section('content')
<div class="auth-wrap">
    <div class="auth-card">

        <div class="auth-header">
            <img src="/agent/assets/logo.png" class="toplogo" alt="PIF Portal Logo">

            <h1 class="auth-title">Request Agent Access</h1>
            <p class="auth-subtitle">
                Complete the form below to request access to the Agent Portal.
            </p>
        </div>

        <form method="POST" action="{{ url('/register') }}" class="auth-form">
            @csrf

            {{-- NAME --}}
            <div class="mb-3">
                <label class="form-label" for="name">Full Name</label>
                <div class="auth-input">
                    <span class="auth-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5z"></path>
                            <path d="M3 21a9 9 0 0 1 18 0"></path>
                        </svg>
                    </span>

                    <input id="name" type="text" name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           required placeholder="John Smith">
                </div>
            </div>

            {{-- EMAIL --}}
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <div class="auth-input">
                    <span class="auth-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16v16H4z"></path>
                            <path d="M4 4l8 8l8-8"></path>
                        </svg>
                    </span>

                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           required placeholder="name@company.com">

               
                </div>
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <div class="auth-input">
                    <span class="auth-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                            <path d="M6 11h12v10H6z"></path>
                        </svg>
                    </span>

                    <input id="password" type="password" name="password"
                           class="form-control"
                           required placeholder="••••••••">
                </div>
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div class="mb-2">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <div class="auth-input">
                    <span class="auth-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                            <path d="M6 11h12v10H6z"></path>
                            <path d="M9 16l2 2 4-4"></path>
                        </svg>
                    </span>

                    <input id="password_confirmation" type="password"
                           name="password_confirmation"
                           class="form-control"
                           required placeholder="••••••••">
                </div>
            </div>

            <br>

            <button class="btn btn-primary w-100 auth-btn" type="submit">
                Submit Request
            </button>
        </form>

        <div class="auth-footer">
            <span>Already have access?</span>
            <a href="{{ url('/login') }}" class="auth-link">Back to login</a>
        </div>
    </div>

    <div class="auth-legal">
        © {{ date('Y') }} Pinnacle International Freight Limited — Unit 1 Swannington Road,
        Broughton Astley, Leicester, LE9 6TU, UK. All rights reserved.
    </div>
</div>

@push('scripts')
    @include('layouts.agent.scripts')
@endpush
@endsection

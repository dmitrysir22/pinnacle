@extends('layouts.guest')

@section('content')
<div class="container mt-5" style="max-width:420px;">

    <x-email-not-verified />

    <h4 class="mb-3 text-center">Enter One-Time Password</h4>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Status --}}
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <div class="mb-3">
            <label for="otp" class="form-label">OTP Code</label>
            <input type="text"
                   name="otp"
                   id="otp"
                   class="form-control"
                   required
                   autofocus
                   inputmode="numeric"
                   pattern="[0-9]{6}"
                   placeholder="6-digit code">
        </div>

        <button class="btn btn-primary w-100 mb-3">
            Verify
        </button>
    </form>

    {{-- Resend --}}
    <form method="POST" action="{{ route('otp.resend') }}" id="resend-form">
        @csrf
        <button type="submit"
                id="resend-btn"
                class="btn btn-link w-100"
                disabled>
            Resend OTP (<span id="timer">60</span>s)
        </button>
    </form>

    {{-- Lock countdown --}}
    @if(session('locked_until'))
        <div class="alert alert-warning text-center mt-3">
            Account locked.
            Try again in <strong><span id="lock-timer"></span></strong>
        </div>
    @endif
</div>

{{-- Timers --}}
<script>
    let resendSeconds = 60;
    const resendBtn = document.getElementById('resend-btn');
    const timerEl = document.getElementById('timer');

    const resendInterval = setInterval(() => {
        resendSeconds--;
        timerEl.innerText = resendSeconds;

        if (resendSeconds <= 0) {
            resendBtn.disabled = false;
            resendBtn.innerText = 'Resend OTP';
            clearInterval(resendInterval);
        }
    }, 1000);
</script>

@if(session('locked_until'))
<script>
    const lockUntil = new Date("{{ session('locked_until') }}").getTime();

    const lockInterval = setInterval(() => {
        const now = new Date().getTime();
        const diff = lockUntil - now;

        if (diff <= 0) {
            location.reload();
        }

        const minutes = Math.floor(diff / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);

        document.getElementById('lock-timer').innerText =
            `${minutes}:${seconds.toString().padStart(2,'0')}`;
    }, 1000);
</script>
@endif
@endsection

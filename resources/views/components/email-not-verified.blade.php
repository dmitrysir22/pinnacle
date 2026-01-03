@if(auth()->check() && ! auth()->user()->hasVerifiedEmail())
    <div class="alert alert-warning text-center">
        ⚠️ Your email address is not verified.
        <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
            @csrf
            <button class="btn btn-link p-0 m-0 align-baseline">
                Resend verification email
            </button>
        </form>
    </div>
@endif

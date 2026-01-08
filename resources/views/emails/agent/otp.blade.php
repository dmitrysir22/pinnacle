@component('mail::message')
# One-Time Password

Hello {{ $user->name ?? 'Agent' }},

Your one-time verification code is:

@component('mail::panel')
# {{ $otp }}
@endcomponent

This code is valid for **10 minutes**.

If you didn’t try to log in, please ignore this email.

Thanks,  
**Pinnacle International Freight**
@endcomponent

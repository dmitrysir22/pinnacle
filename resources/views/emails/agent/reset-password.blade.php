@component('mail::message')
# Hello {{ $user->name }}

We received a request to reset your password for **Pinnacle International Freight | Agent Portal**.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

This link will expire in **60 minutes**.

If you did not request a password reset, please ignore this email.

Thanks,  
**Pinnacle International Freight**
@endcomponent

@component('mail::message')
# Welcome to Pinnacle International Freight | Agent Portal

Hello {{ $user->name }},

Thank you for registering as an agent.

Please verify your email address to continue.

@component('mail::button', ['url' => $url])
Verify Email
@endcomponent

After email verification, your account will require approval by our admin team.

If you did not create this account, no action is required.

Thanks,<br>
**Pinnacle International Freight**
@endcomponent

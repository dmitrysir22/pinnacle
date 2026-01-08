@component('mail::message')
# Your account is now active 🎉

Hello {{ $user->name }},

Your access to the **Pinnacle International Freight – Agent Portal** has been approved.

You can now log in using your email and password.

@component('mail::button', ['url' => url('/login')])
Log in to Agent Portal
@endcomponent

If you have any issues accessing the portal, please contact our team.

Thanks,  
**Pinnacle International Freight**
@endcomponent

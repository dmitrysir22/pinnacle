@component('mail::message')
# New Agent Registration

A new agent has registered and is awaiting approval.


**Name:** {{ $agent->name }}  
**Email:** {{ $agent->email }}  
**Registered at:** {{ $agent->created_at->format('d M Y H:i') }}


@component('mail::button', ['url' => config('app.url') . 'manage2026_pif/user/' . $agent->id . '/edit'])
Review Agent
@endcomponent

Thanks,  
**Pinnacle Agent Portal**
@endcomponent

@component('mail::message')

Welcome {{ $name }},
Congratulations! you have been approved.

Now you can login and start using our services.

@component('mail::button', ['url' => 'https://' . env('APP_URL') . '/auth/login'])
Login Now!
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

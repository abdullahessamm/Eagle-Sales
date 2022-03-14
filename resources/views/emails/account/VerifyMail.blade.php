@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', [
    'url' => 'https://' . env('APP_URL') . "/verify-mail/$userID?token=$token"
])
Verify Now!
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

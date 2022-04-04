@component('mail::message')
# Item Approval

Dear {{ $user->f_name }}, your item "{{ $item->name }}" has been approved, you can now start selling it.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

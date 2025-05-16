@component('mail::message')
# Reminder: {{ $title }}

{{ $description }}

@component('mail::button', ['url' => $url])
View Card
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
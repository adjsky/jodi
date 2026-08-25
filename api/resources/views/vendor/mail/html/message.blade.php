<x-mail::layout>
<img src="{{ config("app.url") }}/jodi.png" width="140" height="61" class="logo">

{{ __('mail.hello') }}

{!! $slot !!}

{{ __('mail.thanks') }},

{{ config('app.name') }}
</x-mail::layout>

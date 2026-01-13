<x-mail::layout>
<img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" width="140" height="61" class="logo">

{{ __('mail.hello') }}

{!! $slot !!}

{{ __('mail.thanks') }},

{{  config("app.name") }}
</x-mail::layout>

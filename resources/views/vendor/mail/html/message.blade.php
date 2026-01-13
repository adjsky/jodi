<x-mail::layout>
<img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" width="120" height="55" class="logo">

{{ __('mail.hello') }}

{!! $slot !!}

{{ __('mail.thanks') }},

{{  config("app.name") }}
</x-mail::layout>

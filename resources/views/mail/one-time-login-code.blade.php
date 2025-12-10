<x-mail::message>
    # {{ __('One time login code') }}

    {{ __('This is your one-time login code to use on :url.', ['url' => config('app.url')]) }}

    **{{ $otp }}**
</x-mail::message>

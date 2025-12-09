<x-mail::message>
    # {{ __('You were invited to Jodi') }}

    {{ __('User :email invited you', ['email' => $email]) }}

    <x-mail::button :url="$url">
        {{__("Join")  }}
    </x-mail::button>
</x-mail::message>

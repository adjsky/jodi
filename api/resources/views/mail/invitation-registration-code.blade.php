<x-mail::message>
    {{ __('mail.invitation_registration_code.paragraphs.code', ['code' => $code]) }}

    {{ __('mail.invitation_registration_code.paragraphs.ahtung') }}
</x-mail::message>

<x-mail::message>
{{ __('mail.invite_to_jodi.paragraphs.who', ['app' => $app, 'email' => $inviter->email]) }}

{{ __('mail.invite_to_jodi.paragraphs.join', ["name" => $inviter->name]) }}

<x-mail::button :url="$url">
{{__("mail.invite_to_jodi.actions.create")  }}
</x-mail::button>

{{ __('mail.invite_to_jodi.paragraphs.ahtung', ["email" => $inviter->email]) }}
</x-mail::message>

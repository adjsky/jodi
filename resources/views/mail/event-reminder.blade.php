<x-mail::message>
{{
    __(
        "mail.event_reminder.paragraphs.reminder",
        ["title" => $event->title, "time" => $time]
    )
}}
</x-mail::message>

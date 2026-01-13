<x-mail::message>
{{
    __("mail.event_reminder.paragraphs.reminder",
    ["title" => $event->title, "date" => $event->starts_at->isoFormat('D MMMM, HH:mm')])
}}
</x-mail::message>

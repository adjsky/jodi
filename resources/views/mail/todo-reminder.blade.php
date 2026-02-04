<x-mail::message>
{{
    __("mail.todo_reminder.paragraphs.reminder",
    ["title" => $todo->title, "date" => $todo->scheduled_at->isoFormat('D MMMM, HH:mm')])
}}
</x-mail::message>

<x-mail::message>
{{
    __(
        "mail.todo_reminder.paragraphs.reminder",
        ["title" => $todo->title, "time" => $time]
    )
}}
</x-mail::message>

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Todo\Notifications\TodoReminder;
use App\Models\Todo;
use Illuminate\Console\Command;

class RemindTodosCommand extends Command
{
    protected $signature = 'jodi:remind:todos';

    protected $description = 'Remind users about planned todos.';

    public function handle(): void
    {
        Todo::with('user')
            ->where('notify_at', '<=', now())
            ->where('scheduled_at', '<=', now())
            ->where('notify_status', '!=', 'sent')
            ->chunk(100, function ($todos) {
                $sent = [];

                foreach ($todos as $todo) {
                    if (! $todo->user) {
                        continue;
                    }

                    $todo->user->notify(new TodoReminder($todo));
                    $sent[] = $todo->id;
                }

                Todo::whereIn('id', $sent)->update(['notify_status' => 'processing']);
            });
    }
}

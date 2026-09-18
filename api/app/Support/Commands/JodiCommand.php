<?php

declare(strict_types=1);

namespace App\Support\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

abstract class JodiCommand extends Command
{
    /**
     * Run a provided artisan command silently and gracefully handle errors.
     *
     * @param  ?array<string, mixed>  $params
     */
    protected function runArtisan(string $command, string $label, ?array $params = []): bool
    {
        $exitCode = Artisan::call($command, [
            ...$params ?? [],
            '--ansi' => true,
        ]);

        if ($exitCode != 0) {
            $this->error("✘ {$label} failed.");
            $this->line(trim(Artisan::output()));

            return false;
        }

        $this->info("✔ {$label} completed.");

        return true;
    }

    /**
     * Run a provided process silently and gracefully handle errors.
     *
     * @param  string|string[]  $command
     */
    protected function runProcess(string|array $command, string $label): bool
    {
        $result = Process::run($command);

        if ($result->failed()) {
            $this->error("✘ {$label} failed.");
            $this->line(trim($result->errorOutput() ?: $result->output()));

            return false;
        }

        $this->info("✔ {$label} completed.");

        return true;
    }
}

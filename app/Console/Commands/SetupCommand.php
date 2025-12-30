<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SetupCommand extends Command
{
    const DOTENV_PATH = '.env';

    const EXAMPLE_DOTENV_PATH = '.env.example';

    const DB_PATH = 'database/database.sqlite';

    protected $signature = 'jodi:setup
                            {--seed : Indicates if the seed task should be re-run.}
                            {--force : Overwrite existing files.}';

    protected $description = 'Setup application before running it.';

    public function handle(): int
    {
        $this->info('Starting project setup...');
        $this->newLine();

        try {
            $this->setupEnvironment();

            if (! $this->setupDatabase()) {
                return self::FAILURE;
            }

            if (! $this->runArtisan(
                'key:generate',
                label: [
                    'success' => 'application key generated successfully',
                    'error' => 'application key generation failed',
                ]
            )) {
                return self::FAILURE;
            }

            if (! $this->runArtisan(
                'webpush:vapid',
                label: [
                    'success' => 'vapid keys generated successfully',
                    'error' => 'vapid keys generation failed',
                ]
            )) {
                return self::FAILURE;
            }

            $this->newLine();
            $this->info('Project setup successfully completed!');
        } catch (\Throwable $ex) {
            $this->newLine();
            $this->line('Sorry, something went wrong :(');
            $this->newLine();

            $this->components->error($ex->getMessage());
            $this->components->info('See the error log at storage/logs/laravel.log for the full stack trace.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function setupEnvironment(): void
    {
        if (! File::exists(self::DOTENV_PATH) || $this->option('force')) {
            File::copy(self::EXAMPLE_DOTENV_PATH, self::DOTENV_PATH);
            $this->info(sprintf('✔ %s file created.', self::DOTENV_PATH));
        } else {
            $this->comment(sprintf('! %s file already exists.', self::DOTENV_PATH));
        }
    }

    protected function setupDatabase(): bool
    {
        if (File::exists(self::DB_PATH) && ! $this->option('force')) {
            $this->comment(sprintf('! %s already exists.', self::DB_PATH));

            return true;
        }

        File::put(self::DB_PATH, '');
        $this->info(sprintf('✔ %s created.', self::DB_PATH));

        return $this->runArtisan(
            'migrate',
            label: [
                'success' => 'database setup completed',
                'error' => 'database setup failed',
            ],
            params: ['--seed' => $this->option('seed')]
        );
    }

    /**
     * Run a provided artisan command silently and gracefully handle errors.
     *
     * @param  array{success: string, error: string}  $label
     * @param  ?array<string, mixed>  $params
     */
    protected function runArtisan(string $command, array $label, ?array $params = []): bool
    {
        $exitCode = Artisan::call($command, [
            ...$params ?? [],
            '--ansi' => true,
            '--force' => $this->option('force'),
        ]);

        if ($exitCode != 0) {
            $this->error("✘ {$label['error']}.");
            $this->line(trim(Artisan::output()));

            return false;
        }

        $this->info("✔ {$label['success']}.");

        return true;
    }
}

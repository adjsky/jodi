<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Commands\JodiCommand;
use Illuminate\Support\Facades\File;

class SetupCommand extends JodiCommand
{
    protected $signature = 'jodi:setup
                            {--no-keygen : Skip application key generation.}
                            {--no-seed : Skip the seed task.}
                            {--no-ide-helpers : Skip IDE helpers generation.}
                            {--recreate : Overwrite existing files.}';

    protected $description = 'Setup application before running it.';

    const DOTENV_PATH = '.env';

    const EXAMPLE_DOTENV_PATH = '.env.example';

    public function handle(): int
    {
        $this->newLine();
        $this->info('Starting project setup...');
        $this->newLine();

        try {
            $this->setupEnvironment();

            if (! $this->setupDatabase()) {
                return self::FAILURE;
            }

            if (! $this->option('no-keygen') && ! $this->generateApplicationKey()) {
                return self::FAILURE;
            }

            if (! $this->option('no-ide-helpers') && ! $this->generateIdeHelpers()) {
                return self::FAILURE;
            }

            $this->newLine();
            $this->info('Project setup successfully completed!');
            $this->newLine();
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
        if (! File::exists(self::DOTENV_PATH) || $this->option('recreate')) {
            File::copy(self::EXAMPLE_DOTENV_PATH, self::DOTENV_PATH);
            $this->info(sprintf('✔ %s file created.', self::DOTENV_PATH));
        } else {
            $this->comment(sprintf('! %s file already exists.', self::DOTENV_PATH));
        }
    }

    protected function setupDatabase(): bool
    {
        $dbpath = config('database.connections.sqlite.database');

        if (File::exists($dbpath) && ! $this->option('recreate')) {
            $this->comment(sprintf('! %s already exists.', $dbpath));

            return true;
        }

        File::put($dbpath, '');
        $this->info(sprintf('✔ %s created.', $dbpath));

        return $this->runArtisan(
            'migrate',
            label: 'Database setup',
            params: ['--force' => true, '--seed' => ! $this->option('no-seed')]
        );
    }

    protected function generateApplicationKey(): bool
    {
        if (config('app.key') && ! $this->option('recreate')) {
            $this->comment('! Application key already exists.');

            return true;
        }

        return $this->runArtisan(
            'key:generate',
            label: 'Application key generation',
            params: ['--force' => true]
        );
    }

    protected function generateIdeHelpers(): bool
    {
        return $this->runArtisan(
            'jodi:ide-helper',
            label: 'IDE helpers generation'
        );
    }
}

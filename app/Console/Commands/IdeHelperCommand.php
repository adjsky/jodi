<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Commands\JodiCommand;
use Illuminate\Support\Facades\File;

class IdeHelperCommand extends JodiCommand
{
    protected $signature = 'jodi:ide-helper';

    protected $description = 'Generate IDE helper files.';

    public function handle(): int
    {
        File::ensureDirectoryExists(File::dirname(config('ide-helper.filename')));

        if (
            ! $this->runArtisan(
                'ide-helper:models',
                label: 'Autocompletion for models',
                params: ['--write' => true, '--reset' => true, '--write-eloquent-helper' => true],
            )
        ) {
            return self::FAILURE;
        }

        if (
            ! $this->runProcess(
                sprintf('vendor/bin/pint %s', implode(' ', config('ide-helper.model_locations'))),
                label: 'Models formatting',
            )
        ) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}

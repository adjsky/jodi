<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Concerns\HasJodiCookies;
use Illuminate\Http\Request;

class JodiRequest extends Request
{
    use HasJodiCookies;

    const FLASH_DATA_KEY = 'jodi.flash_data';

    public function setFlash(string|array $key, mixed $value = null): void
    {
        if (! is_array($key)) {
            $flash = [$key => $value];
        } else {
            $flash = $key;
        }

        $this->session()->flash(self::FLASH_DATA_KEY, [
            ...$this->getFlash(),
            ...$flash,
        ]);
    }

    public function getFlash(): array
    {
        return $this->session()->get(self::FLASH_DATA_KEY, []);
    }
}

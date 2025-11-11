<?php

declare(strict_types=1);

namespace App\Support\Generators;

class OtpGenerator
{
    public function numeric(int $size): string
    {
        $otp = '';

        for ($i = 0; $i < $size; $i++) {
            $otp .= random_int(0, 9);
        }

        return $otp;
    }
}

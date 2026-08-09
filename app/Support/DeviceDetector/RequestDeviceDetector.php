<?php

declare(strict_types=1);

namespace App\Support\DeviceDetector;

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use Illuminate\Http\Request;

class RequestDeviceDetector
{
    private DeviceDetector $dd;

    public function __construct(Request $request)
    {
        $this->dd = new DeviceDetector(
            $request->userAgent() ?? '',
            ClientHints::factory($request->server())
        );
        $this->dd->setCache(new DeviceDetectorCache);
        $this->dd->parse();
    }

    public function isMobile(): bool
    {
        return $this->dd->isMobile();
    }
}

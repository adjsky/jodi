<?php

declare(strict_types=1);

namespace App\Support\DeviceDetector;

use App\Support\Cache\CacheKey;
use DeviceDetector\Cache\CacheInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DeviceDetectorCache implements CacheInterface
{
    public function fetch(string $id)
    {
        return Cache::get($this->key($id));
    }

    public function contains(string $id): bool
    {
        return Cache::has($this->key($id));
    }

    public function save(string $id, $data, int $lifeTime = 0): bool
    {
        return $lifeTime > 0
             ? Cache::put($this->key($id), $data, $lifeTime)
             : Cache::forever($this->key($id), $data);
    }

    public function delete(string $id): bool
    {
        return Cache::forget($this->key($id));
    }

    public function flushAll(): bool
    {
        return Cache::flush();
    }

    private function key(string $id): string
    {
        return CacheKey::make('device-detector', Str::replace('-', '|', $id));
    }
}

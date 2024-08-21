<?php

namespace Mk4U\Cache;

use Mk4U\Cache\Drivers\Apcu;
use Mk4U\Cache\Drivers\File;

/**
 * Cache Factory
 */
class CacheFactory
{
    private const DRIVER = [
        'apcu' => Apcu::class,
        'file' => File::class,
    ];

    public static function create(?string $driver = null, array $config = [])
    {
        $cache = empty($driver) ? self::DRIVER['file'] : self::DRIVER[$driver];

        return new $cache($config);
    }
}

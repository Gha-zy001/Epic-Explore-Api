<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

abstract class BaseService
{
    /**
     * Cache key prefix.
     */
    protected string $cachePrefix = '';

    /**
     * Get data from cache or store it if not exists.
     */
    protected function remember(string $key, \Closure $callback, int $ttl = null)
    {
        $fullKey = $this->cachePrefix . '.' . $key;
        return $ttl ? Cache::remember($fullKey, $ttl, $callback) : Cache::rememberForever($fullKey, $callback);
    }

    /**
     * Invalidate cache for the given key.
     */
    protected function forget(string $key)
    {
        return Cache::forget($this->cachePrefix . '.' . $key);
    }

    /**
     * Invalidate all cache for the service.
     * Note: Requires cache tags if we want to clear all at once easily.
     */
    public function clearCache()
    {
        // Placeholder for clearing all related cache
    }
}

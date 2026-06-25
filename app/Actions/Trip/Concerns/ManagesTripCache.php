<?php

namespace App\Actions\Trip\Concerns;

use Closure;
use Illuminate\Support\Facades\Cache;

trait ManagesTripCache
{
  protected function rememberTrip(string $key, Closure $callback, int $ttl = 3600): mixed
  {
    return Cache::remember("trips.{$key}", $ttl, $callback);
  }

  protected function forgetTrip(string $key): void
  {
    Cache::forget("trips.{$key}");
  }

  protected function forgetUserTrips(int $userId, ?int $tripId = null): void
  {
    $this->forgetTrip("user.{$userId}.all");

    if ($tripId) {
      $this->forgetTrip("user.{$userId}.trip.{$tripId}");
    }
  }
}

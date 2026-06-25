<?php

namespace App\Actions\Entity\Concerns;

use App\Actions\Entity\EntityType;
use App\Models\State;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait QueriesStateableEntities
{
  protected function remember(EntityType $type, string $key, Closure $callback, ?int $ttl = null): mixed
  {
    $fullKey = $type->cachePrefix() . '.' . $key;

    return $ttl
      ? Cache::remember($fullKey, $ttl, $callback)
      : Cache::rememberForever($fullKey, $callback);
  }

  protected function findStateByName(string $stateName): ?State
  {
    return State::where('name', $stateName)->first();
  }

  protected function mapStateResults(EntityType $type, Collection $items): array|Collection
  {
    return match ($type) {
      EntityType::Place => $items->map(fn ($place) => [
        'id' => $place->id,
        'state_id' => $place->state_id,
        'name' => $place->name,
        'description' => $place->description,
        'address' => $place->address,
        'img_url' => $place->images->pluck('data')->toArray(),
      ])->toArray(),
      EntityType::Hotel => $items->map(fn ($hotel) => [
        'id' => $hotel->id,
        'state_id' => $hotel->state_id,
        'name' => $hotel->name,
        'address' => $hotel->address,
        'img_url' => $hotel->images->pluck('data')->toArray(),
        'rate' => $hotel->rate,
        'price' => $hotel->price,
      ])->toArray(),
      EntityType::Restaurant => $items->map(fn ($restaurant) => [
        'id' => $restaurant->id,
        'state_id' => $restaurant->state_id,
        'name' => $restaurant->name,
        'rate' => $restaurant->rate,
        'address' => $restaurant->address,
        'img_url' => $restaurant->images->pluck('data'),
      ])->toArray(),
      EntityType::Bank => $items,
    };
  }
}

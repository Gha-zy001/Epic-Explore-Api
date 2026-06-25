<?php

namespace App\Actions\Entity;

use App\Http\Resources\BankResource;
use App\Http\Resources\HotelResource;
use App\Http\Resources\PlaceResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Bank;
use App\Models\Hotel;
use App\Models\Place;
use App\Models\Restaurant;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

enum EntityType: string
{
  case Place = 'place';
  case Hotel = 'hotel';
  case Bank = 'bank';
  case Restaurant = 'restaurant';

  public function model(): string
  {
    return match ($this) {
      self::Place => Place::class,
      self::Hotel => Hotel::class,
      self::Bank => Bank::class,
      self::Restaurant => Restaurant::class,
    };
  }

  public function resource(): string
  {
    return match ($this) {
      self::Place => PlaceResource::class,
      self::Hotel => HotelResource::class,
      self::Bank => BankResource::class,
      self::Restaurant => RestaurantResource::class,
    };
  }

  public function cachePrefix(): string
  {
    return match ($this) {
      self::Place => 'places',
      self::Hotel => 'hotels',
      self::Bank => 'banks',
      self::Restaurant => 'restaurants',
    };
  }

  public function listKey(): string
  {
    return match ($this) {
      self::Place => 'allPlaces',
      self::Hotel => 'allHotels',
      self::Bank => 'allBanks',
      self::Restaurant => 'allRestaurants',
    };
  }

  public function listEmptyMessage(): string
  {
    return match ($this) {
      self::Place => 'No Places Yet',
      self::Hotel => 'No Hotels Yet',
      self::Bank => 'No Banks Yet',
      self::Restaurant => 'No Restaurants Yet',
    };
  }

  public function listSuccessMessage(): string
  {
    return match ($this) {
      self::Place => 'Places Fetched Successfully',
      self::Hotel => 'Hotels Fetched Successfully',
      self::Bank => 'Banks Fetched Successfully',
      self::Restaurant => 'Restaurants Fetched Successfully',
    };
  }

  public function stateKey(): string
  {
    return match ($this) {
      self::Place => 'places',
      self::Hotel => 'hotels',
      self::Bank => 'banks',
      self::Restaurant => 'restaurants',
    };
  }

  public function showResponseKey(): ?string
  {
    return match ($this) {
      self::Place => 'placeById',
      default => null,
    };
  }

  public function notFoundLabel(): string
  {
    return match ($this) {
      self::Place => 'Place',
      self::Hotel => 'Hotel',
      self::Bank => 'Bank',
      self::Restaurant => 'Restaurant',
    };
  }

  public function cachesList(): bool
  {
    return $this !== self::Bank;
  }

  public function cachesShow(): bool
  {
    return $this !== self::Bank;
  }

  public function cachesState(): bool
  {
    return true;
  }

  public function usesApiTraitForState(): bool
  {
    return $this === self::Place;
  }

  public function stateColumns(): ?array
  {
    return match ($this) {
      self::Place => ['id', 'state_id', 'name', 'description', 'address'],
      self::Hotel => ['id', 'state_id', 'name', 'rate', 'address', 'price'],
      self::Restaurant => ['id', 'state_id', 'name', 'rate', 'address'],
      self::Bank => null,
    };
  }

  public function stateImageConstraint(): ?Closure
  {
    return match ($this) {
      self::Place => fn ($query) => $query->select('data', 'place_id'),
      self::Hotel => fn ($query) => $query->select('data', 'hotel_id'),
      self::Restaurant => fn ($query) => $query->select('data', 'resturant_id'),
      self::Bank => null,
    };
  }

  public function queryByState(Builder $query): Collection
  {
    $imageConstraint = $this->stateImageConstraint();

    if ($imageConstraint) {
      $query->with(['images' => $imageConstraint]);
    }

    $columns = $this->stateColumns();

    return $columns ? $query->get($columns) : $query->get();
  }
}

<?php

namespace App\Actions\Entity;

use App\Actions\Entity\Concerns\QueriesStateableEntities;
use App\Traits\ApiTrait;

class GetEntitiesByStateAction
{
  use ApiTrait;
  use QueriesStateableEntities;

  public function execute(EntityType $type, string $stateName)
  {
    try {
      $fetch = function () use ($type, $stateName) {
        $state = $this->findStateByName($stateName);

        if (!$state) {
          return null;
        }

        $model = $type->model();
        $items = $type->queryByState($model::where('state_id', $state->id));

        return $this->mapStateResults($type, $items);
      };

      $results = $type->cachesState()
        ? $this->remember($type, "state.{$stateName}", $fetch)
        : $fetch();

      if ($results === null) {
        if ($type->usesApiTraitForState()) {
          return ApiTrait::errorMessage([], 'State not found', 404);
        }

        return response()->json(['error' => 'State not found'], 404);
      }

      if ($type->usesApiTraitForState()) {
        return ApiTrait::data(
          [$type->stateKey() => $results],
          ucfirst($type->stateKey()) . ' fetched by state',
          200
        );
      }

      return [$type->stateKey() => $results];
    } catch (\Throwable) {
      if ($type->usesApiTraitForState()) {
        return ApiTrait::errorMessage([], 'Failed to load ' . $type->stateKey(), 500);
      }

      return ApiTrait::errorMessage([], 'An error occurred', 422);
    }
  }
}

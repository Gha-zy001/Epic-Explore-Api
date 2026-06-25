<?php

namespace App\Actions\Entity;

use App\Actions\Entity\Concerns\QueriesStateableEntities;
use App\Traits\ApiTrait;

class ShowEntityAction
{
  use ApiTrait;
  use QueriesStateableEntities;

  public function execute(EntityType $type, mixed $id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], "Invalid {$type->notFoundLabel()} Id", 400);
    }

    try {
      $model = $type->model();
      $entityId = (int) $id;

      $fetch = fn () => $model::find($entityId);

      $entity = $type->cachesShow()
        ? $this->remember($type, "id.{$entityId}", $fetch, 3600)
        : $fetch();

      if (!$entity) {
        return ApiTrait::errorMessage([], "{$type->notFoundLabel()} Not Found", 404);
      }

      $resource = $type->resource();

      if ($responseKey = $type->showResponseKey()) {
        return ApiTrait::data([$responseKey => new $resource($entity)]);
      }

      return $resource::collection([$entity]);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }
}

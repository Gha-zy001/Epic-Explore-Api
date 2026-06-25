<?php

namespace App\Actions\Entity;

use App\Actions\Entity\Concerns\QueriesStateableEntities;
use App\Traits\ApiTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListEntitiesAction
{
  use ApiTrait;
  use QueriesStateableEntities;

  public function execute(EntityType $type, int $perPage = 10)
  {
    try {
      $page = request()->get('page', 1);
      $model = $type->model();

      $fetch = fn () => $model::paginate($perPage);

      $entities = $type->cachesList()
        ? $this->remember($type, "all.page.{$page}.perPage.{$perPage}", $fetch, 3600)
        : $fetch();

      if (!$entities instanceof LengthAwarePaginator || $entities->count() === 0) {
        return ApiTrait::errorMessage([], $type->listEmptyMessage(), 404);
      }

      $resource = $type->resource();

      return ApiTrait::data(
        [$type->listKey() => $resource::collection($entities)],
        $type->listSuccessMessage(),
        200
      );
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }
}

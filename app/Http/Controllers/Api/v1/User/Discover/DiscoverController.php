<?php

namespace App\Http\Controllers\Api\v1\User\Discover;

use App\Actions\Discover\GetDiscoverContentAction;
use App\Http\Controllers\Controller;

class DiscoverController extends Controller
{
  public function __invoke(GetDiscoverContentAction $action)
  {
    return $action->execute();
  }
}

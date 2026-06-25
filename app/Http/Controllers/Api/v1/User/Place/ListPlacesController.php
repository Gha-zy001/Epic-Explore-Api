<?php

namespace App\Http\Controllers\Api\v1\User\Place;

use App\Actions\Place\ListPlacesAndHotelsAction;
use App\Http\Controllers\Controller;

class ListPlacesController extends Controller
{
  public function __invoke(ListPlacesAndHotelsAction $action)
  {
    return $action->execute();
  }
}

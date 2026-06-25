<?php

namespace App\Http\Controllers\Api\v1\User\Profile;

use App\Actions\Profile\ShowProfileAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowProfileController extends Controller
{
  public function __invoke(Request $request, ShowProfileAction $action)
  {
    return $action->execute($request->user());
  }
}

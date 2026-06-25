<?php

namespace App\Http\Controllers\Api\v1\User\Profile;

use App\Actions\Profile\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;

class UpdateProfileController extends Controller
{
  public function __invoke(ProfileRequest $request, UpdateProfileAction $action)
  {
    return $action->execute(
      $request->user(),
      $request->except('image'),
      $request->file('image'),
    );
  }
}

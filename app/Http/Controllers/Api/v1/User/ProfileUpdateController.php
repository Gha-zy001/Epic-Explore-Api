<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Cloudinary\Api\Upload\UploadApi;

class ProfileUpdateController extends Controller
{
  public function editProfile(ProfileRequest $request)
  {
    $user = $request->user();
    $request->validated();
    $path = 'laravel-cloud/profile-image';
    try {
      if ($request->hasFile('image')) {
        $file = time() . '.' . $request->file('image')->getClientOriginalExtension();
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $publicId = date('Y-m-d_His') . '_' . $fileName;
        $upload = (new UploadApi())->upload(
          $request->file('image')->getRealPath(),
          [
            "public_id" => $publicId,
            "folder" => $path
          ]
        );
        $imagePath = $upload['secure_url'];
        $user->update([
          "image" => $imagePath,
        ]);
      } else {
        $imageName = 'null';
        $user->image = $imageName;
        $user->save();
      }
      $user->update($request->except('image'));

      return ApiTrait::successMessage('Success', 200);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Fail', 422);
    }
  }

  public function show(Request $request)
  {
    $user = $request->user();
    $userData = [
      'name' => $user->name,
      'email' => $user->email,
      'avatar' => $user->image,
      'exp' => $user->exp,
      'level' => $user->level,
    ];
    return ApiTrait::data(compact('userData'));
  }
}

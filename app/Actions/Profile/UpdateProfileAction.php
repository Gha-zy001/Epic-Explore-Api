<?php

namespace App\Actions\Profile;

use App\Models\User;
use App\Traits\ApiTrait;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;

class UpdateProfileAction
{
  use ApiTrait;

  public function execute(User $user, array $data, ?UploadedFile $image = null)
  {
    $path = 'laravel-cloud/profile-image';

    try {
      if ($image) {
        $file = time() . '.' . $image->getClientOriginalExtension();
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $publicId = date('Y-m-d_His') . '_' . $fileName;
        $upload = (new UploadApi())->upload(
          $image->getRealPath(),
          [
            'public_id' => $publicId,
            'folder' => $path,
          ]
        );

        $user->update(['image' => $upload['secure_url']]);
      } else {
        $user->update(['image' => 'null']);
      }

      unset($data['image']);
      $user->update($data);

      return ApiTrait::successMessage('Success', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Fail', 422);
    }
  }
}

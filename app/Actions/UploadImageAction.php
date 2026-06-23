<?php

namespace App\Actions;

use App\Traits\FileUploadTrait;
use Illuminate\Http\UploadedFile;

class UploadImageAction
{
  use FileUploadTrait;

  /**
   * Execute the image upload action.
   *
   * @param UploadedFile|null $file
   * @param string $folder
   * @return string|null
   */
  public function execute(?UploadedFile $file, string $folder = 'images'): ?string
  {
    if ($file instanceof UploadedFile) {
      return $this->uploadFile($file, $folder);
    }

    return null;
  }
}

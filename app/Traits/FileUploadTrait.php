<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait FileUploadTrait
{
  /**
   * Handle file upload to Cloudinary.
   *
   * @param UploadedFile $file
   * @param string $folder
   * @return string $fileUrl
   */
  public function uploadFile(UploadedFile $file, string $folder = 'images'): string
  {
    return cloudinary()->upload($file->getRealPath(), ['folder' => $folder])->getSecurePath();
  }
}

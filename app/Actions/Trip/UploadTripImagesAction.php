<?php

namespace App\Actions\Trip;

use App\Models\Trip;
use App\Traits\ApiTrait;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadTripImagesAction
{
  use ApiTrait;

  public function __construct()
  {
    $this->configureCloudinary();
  }

  public function execute(int $userId, int $tripId, array $files)
  {
    try {
      if (empty($files)) {
        return ApiTrait::errorMessage([], 'No images uploaded', 400);
      }

      $trip = Trip::where('user_id', $userId)->where('id', $tripId)->first();

      if (!$trip) {
        return ApiTrait::errorMessage([], 'Trip not found or access denied', 404);
      }

      $folder = env('CLOUDINARY_FOLDER', 'laravel-cloud/trips-image');
      $imageUrls = [];

      foreach ($files as $file) {
        if (!$file instanceof UploadedFile) {
          continue;
        }

        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($fileName) ?: 'image';
        $publicId = now()->format('Y-m-d_His') . '_' . $safeName . '_' . Str::random(6);

        try {
          $upload = (new UploadApi())->upload($file->getRealPath(), [
            'public_id' => $publicId,
            'folder' => $folder,
          ]);
          $imageUrls[] = $upload['secure_url'];
        } catch (\Throwable $e) {
          Log::error('Cloudinary upload failed', [
            'trip_id' => $tripId,
            'user_id' => $userId,
            'error' => $e->getMessage(),
          ]);

          return ApiTrait::errorMessage([], 'Image upload failed: ' . $e->getMessage(), 500);
        }
      }

      foreach ($imageUrls as $imageUrl) {
        $trip->images()->create([
          'trip_id' => $tripId,
          'data' => $imageUrl,
        ]);
      }

      return ApiTrait::data(['uploaded' => count($imageUrls)], 'Images uploaded successfully', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Upload failed', 500);
    }
  }

  protected function configureCloudinary(): void
  {
    $cloudName = env('CLOUDINARY_CLOUD_NAME');
    $apiKey = env('CLOUDINARY_API_KEY');
    $apiSecret = env('CLOUDINARY_API_SECRET');

    if ($cloudName && $apiKey && $apiSecret) {
      Configuration::instance([
        'cloud' => [
          'cloud_name' => $cloudName,
          'api_key' => $apiKey,
          'api_secret' => $apiSecret,
        ],
      ]);
    }
  }
}

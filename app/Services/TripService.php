<?php

namespace App\Services;

use App\Models\Trip;
use App\Services\PointService;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TripService extends BaseService
{
    protected string $cachePrefix = 'trips';
    protected PointService $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
        $this->configureCloudinary();
    }

    /**
     * Configure Cloudinary SDK from .env values.
     */
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

    /**
     * Get all trips for the authenticated user.
     */
    public function getUserTrips(int $userId)
    {
        return $this->remember("user.{$userId}.all", function () use ($userId) {
            return Trip::where('user_id', $userId)->get();
        }, 3600);
    }

    /**
     * Get a specific trip belonging to the user.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getSpecificTrip(int $userId, int $tripId)
    {
        return $this->remember("user.{$userId}.trip.{$tripId}", function () use ($userId, $tripId) {
            return Trip::where('user_id', $userId)
                ->where('id', $tripId)
                ->firstOrFail();
        }, 3600);
    }

    /**
     * Create a new trip.
     */
    public function createTrip(array $data)
    {
        $trip = Trip::create($data);

        // Invalidate cache
        $this->forget("user.{$data['user_id']}.all");

        // Award XP for creating trip
        $this->pointService->awardExperience(
            $trip->user,
            100,
            "Created trip: {$trip->title}",
            'xp',
            'trips',
            $trip
        );

        return $trip;
    }

    /**
     * Update an existing trip that belongs to the user.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateTrip(int $userId, int $id, array $data)
    {
        $trip = Trip::where('user_id', $userId)->where('id', $id)->first();

        if (!$trip) {
            return null;
        }

        $trip->update($data);

        // Invalidate caches
        $this->forget("user.{$userId}.all");
        $this->forget("user.{$userId}.trip.{$id}");

        return $trip;
    }

    /**
     * Delete a trip that belongs to the user.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteTrip(int $userId, int $id)
    {
        $trip = Trip::where('user_id', $userId)->where('id', $id)->first();

        if (!$trip) {
            return false;
        }

        $deleted = $trip->delete();

        if ($deleted) {
            $this->forget("user.{$userId}.all");
            $this->forget("user.{$userId}.trip.{$id}");
        }

        return (bool) $deleted;
    }

    /**
     * Upload images for a trip the user owns.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \RuntimeException
     */
    public function uploadTripImages(int $userId, int $tripId, array $files)
    {
        $trip = Trip::where('user_id', $userId)->where('id', $tripId)->first();

        if (!$trip) {
            throw new \RuntimeException('Trip not found or access denied');
        }

        $folder = env('CLOUDINARY_FOLDER', 'laravel-cloud/trips-image');
        $imageUrls = [];

        foreach ($files as $file) {
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
                throw new \RuntimeException('Image upload failed: ' . $e->getMessage());
            }
        }

        foreach ($imageUrls as $imageUrl) {
            $trip->images()->create([
                'trip_id' => $tripId,
                'data' => $imageUrl,
            ]);
        }

        return count($imageUrls);
    }
}

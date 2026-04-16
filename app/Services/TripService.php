<?php

namespace App\Services;

use App\Models\Trip;
use Cloudinary\Api\Upload\UploadApi;

class TripService extends BaseService
{
    protected string $cachePrefix = 'trips';

    /**
     * Get all trips for the authenticated user.
     */
    public function getUserTrips(int $userId)
    {
        return Trip::where('user_id', $userId)->get();
    }

    /**
     * Get a specific trip for the authenticated user.
     */
    public function getSpecificTrip(int $userId, int $tripId)
    {
        return Trip::where('user_id', $userId)->where('id', $tripId)->first();
    }

    /**
     * Create a new trip.
     */
    public function createTrip(array $data)
    {
        return Trip::create($data);
    }

    /**
     * Update an existing trip.
     */
    public function updateTrip(int $id, array $data)
    {
        $trip = Trip::find($id);
        if ($trip) {
            $trip->update($data);
            return $trip;
        }
        return null;
    }

    /**
     * Delete a trip.
     */
    public function deleteTrip(int $id)
    {
        $trip = Trip::find($id);
        if ($trip) {
            return $trip->delete();
        }
        return false;
    }

    /**
     * Upload images for a trip.
     */
    public function uploadTripImages(int $tripId, array $files)
    {
        $config = "CLOUDINARY_URL=cloudinary://215749298241811:gxhrmBq4FeJQnJI2UZbiHwpVSdU@dkduz7amh";
        $trip = Trip::findOrFail($tripId);
        $path = 'laravel-cloud/trips-image';
        
        $imageUrls = [];
        foreach ($files as $file) {
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $fileName = pathinfo($file_name, PATHINFO_FILENAME);
            $publicId = date('Y-m-d_His') . '_' . $fileName;
            
            $upload = (new UploadApi($config))->upload(
                $file->getRealPath(),
                [
                    "public_id" => $publicId,
                    "folder" => $path
                ]
            );
            $imageUrls[] = $upload['secure_url'];
        }

        foreach ($imageUrls as $imageUrl) {
            $trip->images()->create([
                "trip_id" => $tripId,
                "data" => $imageUrl,
            ]);
        }
        
        return count($imageUrls) > 0;
    }
}

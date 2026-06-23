<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiTrait;
use App\Models\Hotel;
use Illuminate\Support\Facades\Cache;
use App\Services\PointService;

class FavoriteController extends Controller
{
    protected PointService $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

  /**
   * Display a listing of the resource.
   */
  public function addtoFavorites($favoritableType, $favoritableId)
  {
    try {

      if ($favoritableType === 'place') {
        $favoritable = Place::findOrFail($favoritableId);
      } elseif ($favoritableType === 'hotel') {
        $favoritable = Hotel::findOrFail($favoritableId);
      } else {
        return ApiTrait::errorMessage([], 'Invalid favoritable type', 422);
      }

      $favorite = Favorite::create([
        'user_id' => auth()->user()->id,
        'favoritable_id' => $favoritableId,
        'favoritable_type' => get_class($favoritable),
      ]);
      Cache::forget('favorites_' . auth()->user()->id);
      Cache::forget("user." . auth()->user()->id . ".favorites");
      $favorites = Favorite::where('user_id', auth()->user()->id)->get();
      Cache::put('favorites_' . auth()->user()->id, $favorites, now()->addMinutes(30));

      // Award XP for favorite
      $this->pointService->awardExperience(
          auth()->user(),
          10,
          "Added {$favoritable->name} to favorites",
          'xp',
          'favorites',
          $favorite
      );

      return ApiTrait::successMessage('Successfully added to favorites', 200);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Fail', 422);
    }
  }

  public function deleteFavorites($favoritableId)
  {
    try {
      Favorite::where('user_id', auth()->user()->id)
        ->where('favoritable_id', $favoritableId)
        ->delete();
      Cache::forget('favorites_' . auth()->user()->id);
      Cache::forget("user." . auth()->user()->id . ".favorites");
      $favorites = Favorite::where('user_id', auth()->user()->id)->get();
      Cache::put('favorites_' . auth()->user()->id, $favorites, now()->addMinutes(30));
      return ApiTrait::successMessage('Successfully deleted', 200);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Fail', 422);
    }
  }

  public function getFavorites()
  {
    try {
      $userId = auth()->user()->id;

      $favorites = Cache::remember('favorites_' . $userId, now()->addMinutes(30), function () use ($userId) {
        return Favorite::where('user_id', $userId)->get();
      });
      // $result = Favorite::where('user_id', auth()->user()->id)
      //   ->get();
      return FavoriteResource::collection($favorites);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'There is no favorites yet', 422);
    }
  }

  public function places_fav(Request $request)
  {
    try {
      $userId = $request->user()->id;
      $favorites = Place::whereHas('favorites', function ($query) use ($userId) {
        $query->where('user_id', $userId)
          ->where('favoritable_type', 'App\\Models\\Place');
      })->with('favorites')->get();

      return FavoriteResource::collection($favorites);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'There is no favorites yet', 422);
    }
  }


  public function hotels_fav(Request $request)
  {
    try {
      $userId = $request->user()->id;
      $favorites = Hotel::whereHas('favorites', function ($query) use ($userId) {
        $query->where('user_id', $userId)
          ->where('favoritable_type', 'App\\Models\\Hotel');
      })->with('favorites')->get();

      return FavoriteResource::collection($favorites);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'There is no favorites yet', 422);
    }
  }
}

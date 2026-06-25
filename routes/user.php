<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\Guider\GuiderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\User\Place\ListPlacesController;
use App\Http\Controllers\Api\v1\User\Place\ShowPlaceController;
use App\Http\Controllers\Api\v1\User\Place\GetPlacesByStateController;
use App\Http\Controllers\Api\v1\User\Place\CheckInPlaceController;
use App\Http\Controllers\Api\v1\User\Favorite\GetFavoritesController;
use App\Http\Controllers\Api\v1\User\Favorite\AddFavoriteController;
use App\Http\Controllers\Api\v1\User\Favorite\DeleteFavoriteController;
use App\Http\Controllers\Api\v1\User\Favorite\GetFavoritePlacesController;
use App\Http\Controllers\Api\v1\User\Favorite\GetFavoriteHotelsController;
use App\Http\Controllers\Api\v1\User\Review\CreateReviewController;
use App\Http\Controllers\Api\v1\User\Review\GetPlaceReviewsController;
use App\Http\Controllers\Api\v1\User\Review\GetHotelReviewsController;
use App\Http\Controllers\Api\v1\User\Trip\ListTripsController;
use App\Http\Controllers\Api\v1\User\Trip\CreateTripController;
use App\Http\Controllers\Api\v1\User\Trip\UpdateTripController;
use App\Http\Controllers\Api\v1\User\Trip\UploadTripImagesController;
use App\Http\Controllers\Api\v1\User\Trip\DeleteTripController;
use App\Http\Controllers\Api\v1\User\Trip\ShowTripController;
use App\Http\Controllers\Api\v1\User\Profile\UpdateProfileController;
use App\Http\Controllers\Api\v1\User\Profile\ShowProfileController;
use App\Http\Controllers\Api\v1\User\Hotel\ListHotelsController;
use App\Http\Controllers\Api\v1\User\Hotel\ShowHotelController;
use App\Http\Controllers\Api\v1\User\Hotel\GetHotelsByStateController;
use App\Http\Controllers\Api\v1\User\Bank\ListBanksController;
use App\Http\Controllers\Api\v1\User\Bank\ShowBankController;
use App\Http\Controllers\Api\v1\User\Bank\GetBanksByStateController;
use App\Http\Controllers\Api\v1\User\Restaurant\ListRestaurantsController;
use App\Http\Controllers\Api\v1\User\Restaurant\ShowRestaurantController;
use App\Http\Controllers\Api\v1\User\Restaurant\GetRestaurantsByStateController;
use App\Http\Controllers\Api\v1\User\Home\HomeController;
use App\Http\Controllers\Api\v1\User\Recommendation\RecommendPlacesController;
use App\Http\Controllers\Api\v1\User\Ranking\LeaderboardController;
use App\Http\Controllers\Api\v1\User\Discover\DiscoverController;
use App\Http\Controllers\Api\v1\User\Quest\ListQuestsController;
use App\Http\Controllers\Api\v1\User\Quest\AcceptQuestController;
use App\Http\Controllers\Api\v1\User\Search\SearchController;
use App\Http\Controllers\Api\v1\User\Auth\RegisterController;
use App\Http\Controllers\Api\v1\User\Auth\LoginController;
use App\Http\Controllers\Api\v1\User\Auth\LogoutController;
use App\Http\Controllers\Api\v1\User\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\v1\User\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('v1/user')->group(function () {
  //Auth
  Route::post('/register', RegisterController::class)->middleware('throttle:5,1');
  Route::post('/login', LoginController::class)->middleware('throttle:5,1');
  Route::middleware('auth:sanctum')->post('/logout', LogoutController::class);
  Route::middleware('auth:sanctum')->post('/edit_profile', UpdateProfileController::class);
  Route::middleware('auth:sanctum')->get('/show_profile', ShowProfileController::class);
  //Reset_Password
  Route::post('/forgot_password', ForgetPasswordController::class)->middleware('throttle:3,1');
  Route::put('/reset_password', ResetPasswordController::class)->middleware('throttle:3,1');


  Route::middleware('auth:sanctum')->get('/recommended', RecommendPlacesController::class);
  Route::middleware('auth:sanctum')->get('/home', HomeController::class);
  Route::middleware('auth:sanctum')->get('/leaderboard', LeaderboardController::class);
  Route::middleware('auth:sanctum')->get('/discover', DiscoverController::class);
  Route::middleware('auth:sanctum')->get('/quests', ListQuestsController::class);
  Route::middleware('auth:sanctum')->post('/quests/accept/{questId}', AcceptQuestController::class);

  //Place routes
  Route::middleware('auth:sanctum')->prefix('place')->group(function () {
    Route::get('/', ListPlacesController::class);
    Route::get('/show/{place}', ShowPlaceController::class);
    Route::post('/check-in', CheckInPlaceController::class);
    Route::get('/{stateName}', GetPlacesByStateController::class);
  });

  //Favorite routes
  Route::middleware('auth:sanctum')->prefix('favorite')->group(function () {
    Route::get('/getFavorites', GetFavoritesController::class);
    Route::post('/add/{favoritableType}/{favoritableId}', AddFavoriteController::class);
    Route::post('/delete/{favoritableId}', DeleteFavoriteController::class);
    Route::get('/places_fav', GetFavoritePlacesController::class);
    Route::get('/hotels_fav', GetFavoriteHotelsController::class);
  });

  //Review routes
  Route::middleware('auth:sanctum')->prefix('review')->group(function () {
    Route::post('/makeReview/{reviewable_type}/{reviewable_id}', CreateReviewController::class);
    Route::get('/getPlaceReview/{place_id}', GetPlaceReviewsController::class);
    Route::get('/getHotelReview/{hotel_id}', GetHotelReviewsController::class);
  });

  //Trip routes
  Route::middleware('auth:sanctum')->prefix('trip')->group(function () {
    Route::get('/get-trip', ListTripsController::class);
    Route::post('/create-trip', CreateTripController::class);
    Route::post('/update-trip/{id}', UpdateTripController::class);
    Route::post('/upload-images/{tripId}', UploadTripImagesController::class);
    Route::post('/delete-trip/{id}', DeleteTripController::class);
    Route::get('/specific-trip/{tripId}', ShowTripController::class);
  });

  //hotel routes
  Route::middleware('auth:sanctum')->prefix('hotel')->group(function () {
    Route::get('/get-hotel/{hotelId}', ShowHotelController::class);
    Route::get('/get-hotels', ListHotelsController::class);
    Route::get('/{stateName}', GetHotelsByStateController::class);
  });

  Route::middleware('auth:sanctum')->prefix('bank')->group(function () {
    Route::get('/get-bank/{BankId}', ShowBankController::class);
    Route::get('/get-banks', ListBanksController::class);
    Route::get('/{stateName}', GetBanksByStateController::class);
  });

  Route::middleware('auth:sanctum')->prefix('restaurant')->group(function () {
    Route::get('/get-restaurant/{RestaurantId}', ShowRestaurantController::class);
    Route::get('/get-restaurants', ListRestaurantsController::class);
    Route::get('/{stateName}', GetRestaurantsByStateController::class);
  });

  Route::middleware('auth:sanctum')->prefix('guider_data')->controller(GuiderController::class)->group(function () {
    Route::get('/guider_all_data', 'guider_data');
    Route::get('/guider_specific/{guider_id}', 'guider');
  });


  Route::middleware('auth:sanctum')->prefix('contact')->controller(ContactController::class)->group(function () {
    Route::post('/contact_request/{guider_id}', 'createContactRequest');
  });

  Route::middleware('auth:sanctum')->get('/search', SearchController::class);
});


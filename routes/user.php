<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\Guider\GuiderController;
use App\Http\Controllers\Api\User\TripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\FavoriteController;
use App\Http\Controllers\Api\v1\User\Place\ListPlacesController;
use App\Http\Controllers\Api\v1\User\Place\ShowPlaceController;
use App\Http\Controllers\Api\v1\User\Place\GetPlacesByStateController;
use App\Http\Controllers\Api\v1\User\Place\CheckInPlaceController;
use App\Http\Controllers\Api\v1\User\Hotel\ListHotelsController;
use App\Http\Controllers\Api\v1\User\Hotel\ShowHotelController;
use App\Http\Controllers\Api\v1\User\Hotel\GetHotelsByStateController;
use App\Http\Controllers\Api\v1\User\Bank\ListBanksController;
use App\Http\Controllers\Api\v1\User\Bank\ShowBankController;
use App\Http\Controllers\Api\v1\User\Bank\GetBanksByStateController;
use App\Http\Controllers\Api\v1\User\Restaurant\ListRestaurantsController;
use App\Http\Controllers\Api\v1\User\Restaurant\ShowRestaurantController;
use App\Http\Controllers\Api\v1\User\Restaurant\GetRestaurantsByStateController;
use App\Http\Controllers\Api\User\ProfileUpdateController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\v1\User\Home\HomeController;
use App\Http\Controllers\Api\v1\User\Recommendation\RecommendPlacesController;
use App\Http\Controllers\Api\v1\User\Ranking\LeaderboardController;
use App\Http\Controllers\Api\v1\User\Discover\DiscoverController;
use App\Http\Controllers\Api\v1\User\Quest\ListQuestsController;
use App\Http\Controllers\Api\v1\User\Quest\AcceptQuestController;
use App\Http\Controllers\Api\User\SearchController;
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
  Route::middleware('auth:sanctum')->post('/edit_profile', [ProfileUpdateController::class, 'editProfile']);
  Route::middleware('auth:sanctum')->get('/show_profile', [ProfileUpdateController::class, 'show']);
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
  Route::middleware('auth:sanctum')->prefix('favorite')->controller(FavoriteController::class)->group(function () {
    Route::get('/getFavorites', 'getFavorites');
    Route::post('/add/{favoritableType}/{favoritableId}', 'addtoFavorites');
    Route::post('/delete/{favoritableId}', 'deleteFavorites');
    Route::get('/places_fav', 'places_fav');
    Route::get('/hotels_fav', 'hotels_fav');
  });

  //Review routes
  Route::middleware('auth:sanctum')->prefix('review')->controller(ReviewController::class)->group(function () {
    Route::post('/makeReview/{reviewable_type}/{reviewable_id}', 'createReview');
    Route::get('/getPlaceReview/{place_id}', 'getPlaceReviews');
    Route::get('/getHotelReview/{hotel_id}', 'getHotelReviews');
  });

  //Trip routes
  Route::middleware("auth:sanctum")->prefix('trip')->controller(TripController::class)->group(function () {
    Route::get('/get-trip', 'getTrip');
    Route::post('/create-trip', 'createTrip');
    Route::post('/update-trip/{id}', 'updateTrip');
    Route::post('/upload-images/{tripId}', 'uploadImages');
    Route::post('/delete-trip/{id}', 'deleteTrip');
    Route::get('/specific-trip/{tripId}', 'specificTrip');
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

  Route::get('/search', [SearchController::class, 'search'])->middleware('auth:sanctum');
});

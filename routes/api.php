<?php

use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\SocialMediaAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('/login', [LoginRegisterController::class, 'login']);

Route::post('/register', [LoginRegisterController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get(
        '/profile',
        [SocialMediaAPI::class, 'ownprofile']
    );
    Route::get('/userprofile/{user_id}', [SocialMediaAPI::class, 'userProfile']);
    Route::post('/sendfriendrequest', [SocialMediaAPI::class, 'sendFriendRequest']);

    Route::get('/searchuser/{search_param}', [SocialMediaAPI::class, 'search']);
    Route::post('/acceptrequest', [SocialMediaAPI::class, 'acceptrequest']); //laravel acts up with Put request
});

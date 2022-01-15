<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\PostController;


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
Route::post('login', [AuthUserController::class, 'login']);
Route::post('register', [AuthUserController::class, 'register']);

Route::group(['middleware' => ['token']], function() {
    Route::get('get_user', [AuthUserController::class, 'getUser']);
    // post routes
    Route::apiResource('posts', PostController::class);
    Route::get('postApprove/{post}', [PostController::class, 'approve']);
});

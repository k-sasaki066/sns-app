<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['firebase.auth'])->group(function () {
    Route::get('/v1/posts', [PostController::class, 'index']);
    Route::post('/v1/posts', [PostController::class, 'store']);
    Route::delete('/v1/posts/{id}', [PostController::class, 'destroy']);

    Route::post('/v1/posts/{id}/like', [LikeController::class, 'toggleFavorite']);

    Route::get('/v1/posts/{id}/comments', [CommentController::class, 'index']);
    Route::post('/v1/posts/{id}/comments', [CommentController::class, 'store']);
});

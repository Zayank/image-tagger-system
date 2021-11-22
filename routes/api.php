<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
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

// Public routes

//signup
Route::post('/user/register', [AuthController::class, 'register']);
//signin
Route::post('/user/login', [AuthController::class, 'login']);
//public posts
Route::get('/posts', [PostController::class, 'index']);


//Protected routes

Route::group(['middleware' => ['auth:sanctum']], function () {
    //all user posts
    Route::get('/myposts', [PostController::class, 'users_posts']);
    //upload file
    Route::post('/posts/upload-file', [PostController::class, 'upload_file']);
    //create post
    Route::post('/posts', [PostController::class, 'store']);
    //post details
    Route::get('/posts/{id}', [PostController::class, 'show'])->whereNumber('id');
    //update post
    Route::put('/posts/{id}', [PostController::class, 'update'])->whereNumber('id');    
    
    //logout
    Route::post('/user/logout', [AuthController::class, 'logout']);
    
});
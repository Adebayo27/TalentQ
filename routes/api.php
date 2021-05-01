<?php

use App\Http\Controllers\API\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\RequestController;
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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::get('unauthorized', [RegisterController::class, 'unauthorized'])->name('unauthorized');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('/get_requests', [RequestController::class, 'get_requests']);
    Route::get('/get_my_requests', [RequestController::class, 'get_my_requests']);
    Route::post('/create_category', [AdminController::class, 'create_category']);
    Route::post('/make_request', [RequestController::class, 'make_request']);
    Route::post('/respond_to_request', [RequestController::class, 'respond_to_request']);
    Route::get('/view_response/{id}', [RequestController::class, 'view_response']);
    Route::get('/view_response/{id}/{response_id}/{status}', [RequestController::class, 'view_response']);
    Route::get('/get_pending_responses', [RequestController::class, 'get_pending_responses']);
    Route::get('/get_my_photos', [RequestController::class, 'get_my_photos']);
});

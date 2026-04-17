<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ListController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout',[AuthController::class, 'logout']);
    Route::get('/me',[AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);
    Route::put('/me/password',[AuthController::class, 'updatePassword']);

    Route::get('/boards',[BoardController::class, 'index']);
    Route::post('/boards',[BoardController::class, 'store']);
    Route::get('/boards/{board}',[BoardController::class, 'show']);
    Route::put('/boards/{board}',[BoardController::class, 'update']);
    Route::delete('/boards/{board}',[BoardController::class, 'destroy']);
    Route::post('/boards/{board}/members',[BoardController::class, 'addMember']);
    Route::delete('/boards/{board}/members/{user}',[BoardController::class, 'removeMember']);

    Route::get('/boards/{board}/lists',[ListController::class, 'index']);
    Route::post('/boards/{board}/lists',[ListController::class, 'store']);
    Route::post('/boards/{board}/lists/reorder',[ListController::class, 'reorder']);
    Route::put('/boards/{board}/lists/{list}',[ListController::class, 'update']);
    Route::delete('/boards/{board}/lists/{list}',[ListController::class, 'destroy']);
});
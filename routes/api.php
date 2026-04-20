<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\CardController;


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

    Route::get('/lists/{list}/cards',[CardController::class, 'index']);
    Route::post('/lists/{list}/cards',[CardController::class, 'store']);
    Route::get('/cards/{card}',[CardController::class, 'show']);
    Route::put('/cards/{card}',[CardController::class, 'update']);
    Route::delete('/cards/{card}',[CardController::class, 'destroy']);
    Route::post('/cards/{card}/move',[CardController::class, 'move']);
    Route::post('/cards/{card}/assign',[CardController::class, 'assign']);
    Route::post('/cards/{card}/complete',[CardController::class, 'toggleComplete']);
});
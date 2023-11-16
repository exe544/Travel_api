<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TravelController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Auth\LoginController;

Route::get('travels', [TravelController::class, 'index']);
Route::get('travels/{travel:slug}/tours', [TourController::class, 'index']);

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
  Route::middleware('role:admin')->group(function () {
      Route::post('travels', [Admin\TravelController::class, 'store']);
      Route::post('travels/{travel}/tours', [Admin\TourController::class, 'store']);
  });
   Route::put('travels/{travel}', [Admin\TravelController::class, 'update'])->middleware('role:editor');
});

Route::post('login', LoginController::class);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Main\KeywordController;
use App\Http\Controllers\Main\MasjidReviewController;
use App\Http\Controllers\Main\PrayerTimeController;
use App\Http\Controllers\Fe\FeProductController;
use App\Http\Controllers\Fe\FeMasjidController;


Route::middleware('api')->group(function (){
    Route::prefix('v1')->group(function (){
        Route::prefix('users')->group(function (){
            Route::prefix('me')->group(function () {
                Route::get('profile', [AuthController::class, 'userProfile']);
            });
        });

        Route::prefix('masjids')->group(function(){
            Route::get('{id}/photos', [FeMasjidController::class, 'getMasjidPhoto']);
            Route::get('{id}/reviews', [FeMasjidController::class, 'getMasjidReviews']);
        });

        Route::get('prayTime/{city}', [PrayerTimeController::class, 'getPrayTime']);

        Route::prefix('reviewMasjid')->group(function (){
            Route::post('store/{masjidId}', [MasjidReviewController::class, 'store']);
        });

        Route::prefix('keyword')->group(function (){
            Route::post('store', [KeywordController::class, 'store']);
            Route::get('all', [KeywordController::class, 'index']);
            Route::get('detail/{keywordId}', [KeywordController::class, 'show']);
            Route::put('update/{keywordId}', [KeywordController::class, 'update']);
            Route::delete('delete/{keywordId}', [KeywordController::class, 'destroy']);
        });

        Route::prefix('products')->group(function (){
            Route::get('category', [FeProductController::class, 'getProductCategory']);
        });

        Route::prefix('productInformation')->group(function(){

        });

        Route::prefix('forums')->group(function (){

        });

        Route::prefix('comments')->group(function (){

        });
    });
});

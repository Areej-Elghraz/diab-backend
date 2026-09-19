<?php

use App\Enums\TokenAbilityEnum;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Auth\ResendOtpController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\SocialLinkController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\PhoneNumberController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::post('/login', LoginController::class)->name('auth.login');
Route::post('/refresh-token', RefreshTokenController::class)->name('auth.refresh-token')
    ->middleware(['auth:sanctum', 'verified', 'authorized', 'ability:' . TokenAbilityEnum::remember_token->value]); // 'throttle:5,1'
Route::post('/forget-password', ForgetPasswordController::class)->name('auth.forget-password');
Route::post('/resend-otp', ResendOtpController::class)->name('auth.resend-otp');
Route::post('/verify-otp', VerifyOtpController::class)->name('auth.verify-otp');
Route::post('/reset-password', ResetPasswordController::class)->name('auth.reset-password');

$resources = [
    'categories'    => CategoryController::class,
    'products'      => ProductController::class,
    'social-links'  => SocialLinkController::class,
    'phone-numbers' => PhoneNumberController::class,
    'banners'       => BannerController::class,
];

foreach ($resources as $uri => $controller) {
    Route::apiResource($uri, $controller)->only(['index']);
}
Route::controller(ProductImageController::class)->name('product-images.')->group(function () {
    Route::get('/products/{product}/images', 'index')->name('index');
    Route::get('/products/{product}/images/{productImage}', 'show')->name('show');
});

// what will be shown for admins 
Route::middleware(['auth:sanctum', 'verified', 'authorized', 'ability:' . TokenAbilityEnum::access_token->value])->group(function () use ($resources) {

    // logout
    Route::post('/logout', LogoutController::class)->name('auth.logout');

    foreach ($resources as $uri => $controller) {
        Route::apiResource($uri, $controller)->except(['index', 'show']);
    }

    Route::controller(ProductController::class)->name('products.')->group(function () {
        Route::get('products/trashed', 'indexTrashed')
            ->name('index-trashed');
        Route::get('products/trashed/{id}', 'showTrashed')
            ->whereNumber('id')
            ->name('show-trashed');
        Route::put('products/restore/{id}', 'restore')
            ->whereNumber('id')
            ->name('restore');
        Route::delete('products/force-delete/{id}', 'forceDelete')
            ->whereNumber('id')
            ->name('force-delete');
    });

    Route::controller(ProductImageController::class)->name('product-images.')->group(function () {
        Route::post('/products/{product}/images', 'store')->name('store');
        Route::delete('/products/{product}/images', 'destroyAll')->name('destroy-all');
        Route::put('/products/{product}/images/{productImage}', 'update')->name('update');
        Route::delete('/products/{product}/images/{productImage}', 'destroy')->name('destroy');
    });

    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/profile', 'show')->name('show');
        Route::put('/profile', 'updateData')->name('update-data');
        Route::put('/profile/password', 'updatePassword')->name('update-password');
    });
});


Route::get('/phone-numbers/type/{type}', [PhoneNumberController::class, 'showByType']);
foreach ($resources as $uri => $controller) {
    Route::apiResource($uri, $controller)->only(['show']);
}

Route::fallback(function () {
    return response()->json(['message', __('messages.404_not_found')], 404);
});

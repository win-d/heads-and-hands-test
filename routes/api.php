<?php

use App\Http\Controllers\Api\ShortLinkController;
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

Route::middleware('auth:api')->group(function() {
    Route::get('/links', [ShortLinkController::class, 'index'])->name('api.links.index');
    Route::get('/links/{id}', [ShortLinkController::class, 'show'])->name('api.links.show');
    Route::post('/links', [ShortLinkController::class, 'store'])->name('api.links.store');
    Route::patch('/links/{id}/ban', [ShortLinkController::class, 'ban'])->name('api.links.ban');
});

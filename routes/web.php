<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(["auth"])->group(function () {
    Route::get("/", [HomeController::class, "dashboard"])->name("dashboard");
    Route::post("/calendar", [HomeController::class, "calendar"])->name("calendar");

    Route::name("masters.")->group(function () {
        Route::get("statistics", [MasterController::class, "statistics"])->name("statistics");
    });
});

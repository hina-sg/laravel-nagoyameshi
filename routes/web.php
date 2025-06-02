<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\UserHomeController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('users/index', [UserController::class, "index"])->name('users.index');
    Route::get("users/show/{id}", [UserController::class, "show"])->name('users.show');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('restaurants', RestaurantController::class);
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('categories', CategoryController::class);
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('company', CompanyController::class);
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('terms', TermController::class);
});

Route::group(['middleware' => 'guest:admin'], function() {
    Route::get("/", [UserHomeController::class, "index"])->name("home");
});
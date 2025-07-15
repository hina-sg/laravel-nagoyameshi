<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\UserController as UserUserController;
use App\Http\Controllers\RestaurantController as UserRestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;




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

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [UserHomeController::class, 'index'])->name('home');
    Route::resource("/restaurants", UserRestaurantController::class)->only(["index", "show"]);
});


Route::group(['middleware' => 'auth:web'], function() {
    Route::resource('user', UserUserController::class);
});

Route::group(["prefix" => "subscription", "as" => "subscription.", "middleware" => ["auth:web", "verified", "\App\Http\Middleware\NotSubscribed::class"]], function () {
    Route::get("/create", [SubscriptionController::class, "create"])->name("create");
    Route::post("/", [SubscriptionController::class, "store"])->name("store");
});

Route::group(["prefix" => "subscription", "as" => "subscription.", "middleware" => ["auth:web", "verified", "\App\Http\Middleware\Subscribed::class"]], function () {
    Route::get("/edit", [SubscriptionController::class, "edit"])->name("edit");
    Route::put("/", [SubscriptionController::class, "update"])->name("update");
    Route::get("/cancel", [SubscriptionController::class, "cancel"])->name("cancel");
    Route::delete("/", [SubscriptionController::class, "destroy"])->name("destroy");
});



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


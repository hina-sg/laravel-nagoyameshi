<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\UserController as UserUserController;
use App\Http\Controllers\RestaurantController as UserRestaurantController;
use App\Http\Controllers\CompanyController as UserCompanyController;
use App\Http\Controllers\TermController as UserTermController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;
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

//管理者としてログインしていない場合
Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [UserHomeController::class, 'index'])->name('home');
    Route::resource("/restaurants", UserRestaurantController::class)->only(["index", "show"]);
    Route::get("/company", [UserCompanyController::class, "index"])->name("company.index");
    Route::get("/terms", [UserTermController::class, "index"])->name("terms.index");
});

//一般ユーザーとしてログインしている場合
Route::group(['middleware' => 'auth:web'], function() {
    Route::resource('user', UserUserController::class);
    Route::resource("restaurants.reviews", ReviewController::class)->only(['index']);
});

//一般ユーザー（メール認証済）、かつ有料会員じゃない場合
Route::group(["prefix" => "subscription", "as" => "subscription.", "middleware" => ["auth:web", "verified", "\App\Http\Middleware\NotSubscribed::class"]], function () {
    Route::get("/create", [SubscriptionController::class, "create"])->name("create");
    Route::post("/", [SubscriptionController::class, "store"])->name("store");
});

//一般ユーザー（メール認証済）、かつ有料会員の場合
Route::group(["prefix" => "restaurants/{restaurant}/reviews", "as" => "restaurants.reviews.", "middleware" => ["auth:web", "verified", "\App\Http\Middleware\Subscribed::class"]], function () {
    Route::get("/create", [ReviewController::class, "create"])->name("create");
    Route::post("/",[ReviewController::class, "store"])->name("store");
    Route::get("/{review}/edit",[ReviewController::class, "edit"])->name("edit");
    Route::patch("/{review}",[ReviewController::class, "update"])->name("update");
    Route::delete("/{review}",[ReviewController::class, "destroy"])->name("destroy");
});

//一般ユーザー（メール認証済）、かつ有料会員の場合
Route::group(["middleware" => ["auth:web", "verified", "\App\Http\Middleware\Subscribed::class"]], function () {
    Route::resource('reservations', ReservationController::class)->only(["index", "destroy"]);
    Route::resource('restaurants/{restaurant}/reservations', ReservationController::class)->only(["create", "store"])
    ->names(["create" => "restaurants.reservations.create", "store" => "restaurants.reservations.store"]);
    Route::group(["prefix" => "favorites", "as" => "favorites."], function() {
        Route::get("/", [FavoriteController::class, "index"])->name("index");
        Route::post("/{restaurant_id}", [FavoriteController::class, "store"])->name("store");
        Route::delete("/{restaurant_id}", [FavoriteController::class, "destroy"])->name("destroy");
    });
});

//一般ユーザー（メール認証済）、かつ有料会員の場合
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


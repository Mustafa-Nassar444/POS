<?php

use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\Client\OrderController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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

Route::group(['prefix' => LaravelLocalization::setLocale(),
    'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]], function()
{
    Route::name('dashboard.')->prefix('dashboard')->middleware(['auth'])->group(function (){
        Route::get('/',[DashboardController::class,'index'])->name('index');

        Route::get('/order/{order}/products',[\App\Http\Controllers\Dashboard\OrderController::class,'products'])->name('orders.products');

        Route::resources([
            '/categories'=>CategoryController::class,
            '/products'=>ProductController::class,
            '/clients'=>ClientController::class,
            '/clients.orders'=>OrderController::class,
            '/orders'=>\App\Http\Controllers\Dashboard\OrderController::class,
            '/users'=>UserController::class
        ]);

    });

});



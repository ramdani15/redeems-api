<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::name('api.v1.')
->namespace('Api\\V1')
->prefix('v1')
->group(function () {
    Route::controller('AuthController')->prefix('auth')->as('auth.')->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/signup', 'signup')->name('signup');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', 'logout')->name('logout');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        /** Users */
        Route::delete('users/{id}/delete-permanent', 'UserController@deletePermanent')->name('users.delete-permanent');
        Route::resource('users', 'UserController', ['as' => 'users']);

        /** Profile */
        Route::controller('ProfileController')->prefix('profile')->as('profile.')->group(function () {
            Route::get('/', 'profile')->name('index');
            Route::patch('/', 'update')->name('update');
        });

        /** Gifts */
        Route::controller('GiftController')->prefix('gifts')->as('gifts.')->group(function () {
            Route::get('liked', 'liked')->name('liked');
            Route::get('rated', 'rated')->name('rated');
            Route::post('redeem', 'redeems')->name('redeems');
            Route::get('redeem', 'orders')->name('redeems.orders');

            Route::delete('{id}/delete-permanent', 'deletePermanent')->name('gifts.delete-permanent');
            Route::post('{id}/like', 'like')->name('like');
            Route::post('{id}/rating', 'rating')->name('rating');
            Route::post('{id}/redeem', 'redeem')->name('redeem');
            Route::post('{id}/rating', 'rating')->name('rating');
        });
        Route::resource('gifts', 'GiftController', ['as' => 'gifts']);
    });
});

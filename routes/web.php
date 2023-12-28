<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;

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

Route::get('/', [WelcomeController::class, 'index']);
Route::get('/accept', [WelcomeController::class, 'handleConsentToken']);
// Route::get('/accept', function (Request $request) {
//   // config('ebay.consent_token');
//   \Config::set('ebay.consent_token', $request->code);
//   dd($request->all(), config('ebay.consent_token'));
//     return view('welcome');
// });
Route::get('/decline', function (Request $request) {
  dd($request->all());
    return view('welcome');
});






Auth::routes(['verify' => true]);

// ADMIN AUTHORIZATION
Route::get('/admin/login', 'Admin\Auth\LoginController@login')->name('admin.auth.login');
Route::post('/admin/login', 'Admin\Auth\LoginController@loginSubmit')->name('admin.auth.loginSubmit');
Route::get('/admin/logout', 'Admin\Auth\LoginController@logout')->name('admin.auth.logout');

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', function () {
    return redirect('/dashboard');
});
// SOCIAL LOGIN
// Google
Route::get('auth/google', 'Auth\Social\GoogleController@redirectToGoogle');
Route::get('auth/google/callback', 'Auth\Social\GoogleController@handleGoogleCallback');


// Test
Route::get('test/easyship/test', 'TestController@easyshipTest');
Route::get('test/shopify/test', 'TestController@shopifyTest');
Route::get('test/walmart/products', 'TestController@walmartProducts');
Route::get('test/amazon-pay', 'TestController@amazonPay');
Route::get('test/renew-subs', 'TestController@renewSubs');
Route::get('test/noti-check', 'TestController@notiCheck');
Route::get('test/ebay', 'TestController@ebay');
Route::get('test/dotest', 'TestController@dotest');
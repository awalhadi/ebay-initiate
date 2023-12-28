<?php

use App\Http\Controllers\CategoryController;
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
Route::get('/decline', function (Request $request) {
  dd($request->all());
  return view('welcome');
});


// category
Route::get('/get-default-category-id', [CategoryController::class, 'getDefaultCategory']);
Route::get('/get-category-tree-by-id', [CategoryController::class, 'getCategoryTreeById']);



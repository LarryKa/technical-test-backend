<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::resource('/contact', ContactController::class, [
    'only' => ['index','store','show','update','destroy']
]);

Route::get('/search/{searchTerm}', [ContactController::class, 'search']);

Route::resource('/phone', PhoneController::class, [
    'only' => ['store','update','destroy']
]);

Route::resource('/email', EmailController::class, [
    'only' => ['store','update','destroy']
]);

Route::resource('/address', AddressController::class, [
    'only' => ['store','update','destroy']
]);
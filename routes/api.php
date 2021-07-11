<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'userLogin']);
Route::post('/register', [AuthController::class, 'userRegitration']);

Route::group(['middleware'=>['user']], function () {

});

Route::group(['middleware'=>['admin']], function () {
    // Route::namespace('admin')->prefix('admin')->group(function() {
    //Route::get(User::ADMIN_ROUTE, [AdminController::class, 'index'])->name('admin');
});

Route::group(['namespace' => 'admin', 'prefix' => 'admin'], function() {
    Route::group(['middleware'=>['admin']], function () {
        
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UsersController, DetailVehicleController, testController, VehicleListController, RatingController, WishlistController, ChatController};

/*s
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/vehicle_list');
Route::redirect('/dashboard', '/vehicle_list');
Route::get('test', [testController::class, 'test']);
Route::get('generateRandData', [testController::class, 'generateRandData']);
Route::post('login', [UsersController::class, 'login'])->name('user.login');
Route::post('logout', [UsersController::class, 'logout'])->name('user.logout');
Route::post('register', [UsersController::class, 'register'])->name('user.register');
Route::post('update', [UsersController::class, 'update'])->name('user.update');

// list kendaraan
Route::get('vehicle_list', [VehicleListController::class, 'index'])->name('vehicle_list.index');
Route::get('getVehicleList', [VehicleListController::class, 'show'])->name('vehicle_list.show');
Route::get('getBrandModel', [VehicleListController::class, 'getBrandModel'])->name('vehicle_list.getBrandModel');

// detail kendaraan
Route::get('vehicle_detail/{id}', [DetailVehicleController::class, 'show'])->name('vehicle_detail.show');
Route::post('rating/{id}', [RatingController::class, 'show'])->middleware('auth')->name('vehicle_detail.rating');
Route::post('wishlist/{id}', [WishlistController::class, 'show'])->middleware('auth')->name('vehicle_detail.wishlist');

// wishlist
Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.getlist');

// chat
Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('profile');
Route::get('mylist');

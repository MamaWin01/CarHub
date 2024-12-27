<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UsersController, DetailVehicleController, testController,
    VehicleListController, RatingController, WishlistController, ChatController,
    MyListController, ConfigController
};

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

Route::redirect('/', 'vehicle/vehicle_list');
Route::redirect('/dashboard', 'vehicle/vehicle_list');
Route::get('test', [testController::class, 'test']);
Route::get('generateRandData', [testController::class, 'generateRandData']);

// user function
Route::post('user/login', [UsersController::class, 'login'])->name('user.login');
Route::post('user/logout', [UsersController::class, 'logout'])->name('user.logout');
Route::post('user/register', [UsersController::class, 'register'])->name('user.register');
Route::put('user/update', [UsersController::class, 'update'])->name('user.update');
Route::post('user/sendcode', [UsersController::class, 'sendCode'])->name('user.sendCode');

// list kendaraan
Route::get('vehicle/vehicle_list', [VehicleListController::class, 'index'])->name('vehicle_list.index');
Route::get('getVehicleList', [VehicleListController::class, 'show'])->name('vehicle_list.show');
Route::get('getBrandModel', [VehicleListController::class, 'getBrandModel'])->name('vehicle_list.getBrandModel');

// detail kendaraan
Route::get('vehicle/vehicle_detail/{id}', [DetailVehicleController::class, 'show'])->name('vehicle_detail.show');
Route::post('vehicle/rating/{id}', [RatingController::class, 'show'])->middleware('auth')->name('vehicle_detail.rating');
Route::delete('/vehicle/{id}/rating', [RatingController::class, 'destroy'])->name('vehicle_detail.deleteRating');
Route::post('vehicle/wishlist/{id}', [WishlistController::class, 'store'])->middleware('auth')->name('vehicle_detail.wishlist');

// wishlist
Route::get('user/wishlist', [WishlistController::class, 'index'])->name('wishlist.getlist');

// chat
Route::get('user/chat', [ChatController::class, 'index'])->name('chat.index');
// Route::post('user/send', [ChatController::class, 'send'])->name('chat.send');
// Route::post('user/receive', [ChatController::class, 'receive'])->name('chat.receive');
Route::post('user/chat/getChat', [ChatController::class, 'show'])->name('chat.getChat');
Route::get('user/chat/channels', [ChatController::class, 'fetchChannels'])->name('chat.getChannels');

// myList
Route::get('user/mylist', [MyListController::class, 'index'])->name('mylist.index');
Route::post('/user/mylist/store', [MyListController::class, 'store'])->name('mylist.store');
Route::put('/vehicle/update/{id}', [MyListController::class, 'update'])->name('vehicle.update');
Route::delete('/vehicle/delete/{id}', [MyListController::class, 'destroy'])->name('vehicle.delete');

//action
Route::get('action/configVerify', [ConfigController::class, 'configVerify']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{DetailVehicleController, testController, VehicleListController};

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
Route::get('test', [testController::class, 'test']);
Route::get('generateRandData', [testController::class, 'generateRandData']);
Route::get('login');
Route::get('register');
// list kendaraan
Route::get('vehicle_list', [VehicleListController::class, 'index'])->name('vehicle_list.index');
Route::get('getVehicleList', [VehicleListController::class, 'show'])->name('vehicle_list.show');
Route::get('getBrandModel', [VehicleListController::class, 'getBrandModel'])->name('vehicle_list.getBrandModel');

// detail kendaraan
Route::get('vehicle_detail/{id}', [DetailVehicleController::class, 'show'])->name('vehicle_detail.show');

Route::get('list/{id}');
Route::get('wishlist');
Route::get('profile');
Route::get('mylist');

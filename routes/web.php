<?php

use App\Http\Controllers\MasterItemController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [TransactionController::class, 'index'])->name('home');
Route::get('/master-item', [MasterItemController::class, 'index'])->middleware('auth')->name('master-item.index');
Route::get('/master-item/data', [MasterItemController::class, 'dataMasterItem'])->middleware('auth')->name('master-item.data');
Route::post('/master-item/store', [MasterItemController::class, 'store'])->middleware('auth')->name('master-item.store');
Route::get('/master-item/edit/{id}', [MasterItemController::class, 'edit'])->middleware('auth')->name('master-item.edit');
Route::put('/master-item/update/{id}', [MasterItemController::class, 'update'])->middleware('auth')->name('master-item.update');
Route::get('/master-item/show/{id}', [MasterItemController::class, 'show'])->middleware('auth')->name('master-item.show');
Route::delete('/master-item/delete/{id}', [MasterItemController::class, 'delete'])->middleware('auth')->name('master-item.delete');

Route::get('/transaction/data', [TransactionController::class, 'dataTransaction'])->middleware('auth')->name('transaction.data');
Route::post('/transaction/store', [TransactionController::class, 'store'])->middleware('auth')->name('transaction.store');
Route::delete('/transaction/delete/{id}', [TransactionController::class, 'delete'])->middleware('auth')->name('transaction.delete');

Auth::routes();

<?php

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

Route::get('/', [\App\Http\Controllers\ExcelController::class, 'index']);
Route::get('/bang-diem-xls', [\App\Http\Controllers\ExcelController::class, 'upload']);
Route::get('/bang-diem-zip', [\App\Http\Controllers\ExcelController::class, 'uploadZip']);
Route::post('/handle-bang-diem-xls', [\App\Http\Controllers\ExcelController::class, 'handle']);
Route::post('/handle-hang-diem-zip', [\App\Http\Controllers\ExcelController::class, 'handleZip']);

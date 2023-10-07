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
Route::post('/handle-bang-diem-xls', [\App\Http\Controllers\ExcelController::class, 'handle']);
//Route::get('/bang-diem-zip', [\App\Http\Controllers\ExcelController::class, 'uploadZip']);
//Route::post('/handle-hang-diem-zip', [\App\Http\Controllers\ExcelController::class, 'handleZip']);


Route::get('/bang-diem/convert', [\App\Http\Controllers\ExcelController::class, 'showConvert']);
Route::post('/bang-diem/save', [\App\Http\Controllers\ExcelController::class, 'saveConvert']);

Route::get('/bang-diem/search', [\App\Http\Controllers\ExcelController::class, 'search']);
Route::get('/bang-diem/download/{file}', [\App\Http\Controllers\ExcelController::class, 'download']);
Route::get('/bang-diem/delete/{file}', [\App\Http\Controllers\ExcelController::class, 'delete']);
//Route::post('/bang-diem/admin', [\App\Http\Controllers\ExcelController::class, 'handleZip']);


<?php

use App\Http\Controllers\ExcelController;
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

Route::get('/', [ExcelController::class, 'index']);
Route::get('/bang-diem-xls', [ExcelController::class, 'upload']);
Route::post('/handle-bang-diem-xls', [ExcelController::class, 'handle']);
//Route::get('/bang-diem-zip', [\App\Http\Controllers\ExcelController::class, 'uploadZip']);
//Route::post('/handle-hang-diem-zip', [\App\Http\Controllers\ExcelController::class, 'handleZip']);


Route::get('/bang-diem/76d8d7d21619839d64584f264db3674e7472648b', [ExcelController::class, 'showConvert']);
Route::get(
    '/bang-diem/76d8d7d21619839d64584f264db3674e7472648b/download',
    [ExcelController::class, 'downloadAll']
)->name('download_files');
Route::post('/bang-diem/code/check', [ExcelController::class, 'checkCode']);
Route::post('/bang-diem/save', [ExcelController::class, 'saveConvert']);

Route::get('/bang-diem/search', [ExcelController::class, 'search']);
Route::get('/bang-diem/download/{file}', [ExcelController::class, 'download']);
Route::get('/bang-diem/download-filtered', [ExcelController::class, 'downloadFilteredFiles']);
Route::get('/bang-diem/delete/{file}', [ExcelController::class, 'delete']);

Route::get('/khien', [ExcelController::class, 'convertDanhSachDuThi']);
//Route::post('/bang-diem/admin', [\App\Http\Controllers\ExcelController::class, 'handleZip']);


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportItemsController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\EnduseController;
use App\Http\Controllers\ReportController;

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
Route::get('/export-excel', [ImportItemsController::class, 'exportBegbal']);
Route::get('/export-inventory', [ImportItemsController::class, 'exportInventory']);
Route::get('/export-currentinventory', [ImportItemsController::class, 'exportCurrentInventory']);
Route::get('/export-purpose', [PurposeController::class, 'export_purpose']);
Route::get('/export-enduse', [EnduseController::class, 'export_enduse']);
Route::get('/export-receive/{from}/{to}/{item}/{pr}/{category}/{subcategory}/{department}/{enduse}/{purpose}', [ReportController::class, 'export_received']);
Route::get('/export-issue/{from}/{to}/{item}/{pr}/{category}/{subcategory}/{department}/{enduse}/{purpose}', [ReportController::class, 'export_issued']);
Route::get('/export-restock/{from}/{to}/{item}/{pr}/{destination}/{category}/{subcategory}/{department}/{enduse}/{purpose}', [ReportController::class, 'export_restocked']);
Route::get('/export-borrow/{from}/{to}/{item}/{borrowed_from}/{borrowed_by}/{category}/{subcategory}/{department}/{enduse}/{purpose}', [ReportController::class, 'export_borrowed']);
Route::get('/export-backorder/{from}/{to}/{item}/{pr}/{category}/{subcategory}/{department}/{enduse}/{purpose}', [ReportController::class, 'export_backorder']);
Route::get('/export-proverall/{item}/{pr}', [ReportController::class, 'export_proverall']);
Route::get('/export-variants/{item}', [ReportController::class, 'export_variants']);
Route::get('/export-prvariants/{item}/{pr}', [ReportController::class, 'export_prvariants']);
Route::get('/export-stockcard/{item}/{supplier}/{department}/{catalog_no}/{brand}/{running_balance}', [ReportController::class, 'export_stockcard']);
Route::get('/export-stockcarditems/{variant_id}/{item}/{supplier}/{catalog_no}/{brand}/{department}/{running_balance}', [ReportController::class, 'export_stockcarditems']);
Route::get('/{pathMatch}', function(){
    return view('welcome');
})->where('pathMatch',".*");
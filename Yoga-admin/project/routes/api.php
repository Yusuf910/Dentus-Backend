<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\MasterController;
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
 Route::get('manage_user', [MasterController::class, 'manage_user'])->name('manage_user');
 Route::get('add', [MasterController::class, 'add'])->name('add');
 Route::get('importFromExcel', [MasterController::class, 'importFromExcel'])->name('importFromExcel');
 


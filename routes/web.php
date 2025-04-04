<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use App\Models\Item;

Route::view('/', 'home');
Route::view('/dashboard', 'dashboard')->middleware('auth');
Route::view('/sales', 'sales')->middleware('auth');

Route::resource('audits', AuditController::class)->middleware('auth');

Route::get('/items', [ItemController::class, 'index'])->middleware('auth');
Route::get('/items/create', [ItemController::class, 'create'])->middleware('auth');
Route::post('/items', [ItemController::class, 'store'])->middleware('auth');
Route::get('/items/{item}', [ItemController::class, 'show'])->middleware('auth');

Route::get('/items/{item}/edit', [ItemController::class, 'edit'])
    ->middleware('auth')
    ->can('edit', 'item');

Route::patch('/items/{item}', [ItemController::class, 'update'])->middleware('auth');
Route::delete('/items/{item}', [ItemController::class, 'destroy'])->middleware('auth');

Route::get('/pos', [POSController::class, 'index'])->middleware('auth');
Route::post('/pos', [POSController::class, 'store'])->middleware('auth');

//Auth
Route::get('/register', [RegisterUserController::class, 'create']);
Route::post('/register', [RegisterUserController::class, 'store']);

Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);
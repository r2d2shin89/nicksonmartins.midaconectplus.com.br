<?php

use App\Http\Controllers\Api\MessageController as ApiMessageController;
use App\Http\Controllers\Api\PropertyController as ApiPropertyController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::redirect('/admin/login', '/login')->name('admin.login');
Route::redirect('/admin', '/dashboard')->name('admin.dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    Route::livewire('admin/properties', 'pages::admin.properties')->name('admin.properties.index');
    Route::livewire('admin/properties/create', 'pages::admin.property-form')->name('admin.properties.create');
    Route::livewire('admin/properties/{property}/edit', 'pages::admin.property-form')->name('admin.properties.edit');

    Route::livewire('admin/messages', 'pages::admin.messages')->name('admin.messages.index');
});

Route::get('/api/properties', [ApiPropertyController::class, 'index'])->name('api.properties.index');
Route::get('/api/properties/{property}', [ApiPropertyController::class, 'show'])->name('api.properties.show');
Route::post('/api/properties', [ApiPropertyController::class, 'store'])
    ->middleware('admin')
    ->name('api.properties.store');
Route::put('/api/properties/{property}', [ApiPropertyController::class, 'update'])
    ->middleware('admin')
    ->name('api.properties.update');
Route::delete('/api/properties/{property}', [ApiPropertyController::class, 'destroy'])
    ->middleware('admin')
    ->name('api.properties.destroy');

Route::get('/api/messages', [ApiMessageController::class, 'index'])
    ->middleware('admin')
    ->name('api.messages.index');
Route::post('/api/messages', [ApiMessageController::class, 'store'])->name('api.messages.store');
Route::delete('/api/messages/{message}', [ApiMessageController::class, 'destroy'])
    ->middleware('admin')
    ->name('api.messages.destroy');

Route::post('/api/upload', [UploadController::class, 'store'])
    ->middleware('admin')
    ->name('api.upload.store');

require __DIR__.'/settings.php';

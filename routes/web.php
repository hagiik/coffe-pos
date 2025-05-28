<?php

use App\Livewire\OrderPage;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::view('order', 'pages.order')
        ->middleware(['auth', 'verified'])
        ->name('order');
    Route::view('kitchen', 'pages.kitchen')
        ->middleware(['auth', 'verified'])
        ->name('kitchen');
    
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});
require __DIR__.'/auth.php';

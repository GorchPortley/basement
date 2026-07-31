<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/drivers', 'browsedrivers')->name('drivers');
Route::view('/designs', 'browsedesigns')->name('designs');
Route::view('/blog', 'blog')->name('blog');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('/dashboard/drivers', 'dashboarddrivers')->name('dashboarddrivers');
    Route::view('/dashboard/designs', 'dashboarddesigns')->name('dashboarddesigns');
});

require __DIR__.'/settings.php';

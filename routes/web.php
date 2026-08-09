<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

// Every uploaded image and file is served through here, see RouteUrlGenerator.
Route::get('/media/{media}', MediaController::class)->name('media.show');

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

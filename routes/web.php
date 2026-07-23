<?php

use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DesignController;
use App\Livewire\Studio\ComponentForm;
use App\Livewire\Studio\DesignForm;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

/*
| Public library ---------------------------------------------------------- */
Route::get('/designs', [DesignController::class, 'index'])->name('designs.index');
Route::get('/designs/{design:slug}', [DesignController::class, 'show'])->name('designs.show');
Route::get('/components', [ComponentController::class, 'index'])->name('components.index');
Route::get('/components/{component:slug}', [ComponentController::class, 'show'])->name('components.show');

/*
| Authenticated authoring ("studio") -------------------------------------- */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/studio/designs/create', DesignForm::class)->name('studio.designs.create');
    Route::get('/studio/designs/{design}/edit', DesignForm::class)->name('studio.designs.edit');
    Route::get('/studio/components/create', ComponentForm::class)->name('studio.components.create');
    Route::get('/studio/components/{component}/edit', ComponentForm::class)->name('studio.components.edit');
});

/*
| Admin ------------------------------------------------------------------- */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/designs', 'admin.designs')->name('designs');
    Route::view('/components', 'admin.components')->name('components');
});

require __DIR__.'/settings.php';

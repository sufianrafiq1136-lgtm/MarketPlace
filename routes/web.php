<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // The welcome page is publicly accessible.
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Dashboard requires authentication and a verified email address.
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Every route inside this group requires the user to be logged in.

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AJAX endpoint used by the categories table to fetch records without reloading the page.
    Route::get('/categories/data', [CategoryController::class, 'data'])
        ->name('categories.data');

    // Resource routes create the standard index, create, store, edit, update, and delete URLs.
    Route::resource('categories', CategoryController::class);
});

require __DIR__.'/auth.php';

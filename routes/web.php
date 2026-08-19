<?php
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AdController;
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
    
    // Ads
    Route::get('/my-ads', [AdController::class, 'myAds'])->name('ads.my');
    Route::get('/ads/create', [AdController::class, 'create'])->name('ads.create');
    Route::post('/ads', [AdController::class, 'store'])->name('ads.store');
    Route::get('/ads/{ad}/edit', [AdController::class, 'edit'])->name('ads.edit');
    Route::put('/ads/{ad}', [AdController::class, 'update'])->name('ads.update');
    Route::delete('/ads/{ad}', [AdController::class, 'destroy'])->name('ads.destroy');
    
});
Route::get('/ads/data', [AdController::class, 'data'])->name('ads.data');
Route::get('/my-ads/data', [AdController::class, 'myAdsData'])->middleware('auth')->name('ads.my.data');
Route::get('/ads', [AdController::class, 'index'])->name('ads.index');
Route::get('/ads/{ad}', [AdController::class, 'show'])  ->name('ads.show');

// Categories are readable by everyone.
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/data', [CategoryController::class, 'data'])->name('categories.data');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Category changes are admin-only.
Route::middleware('auth')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__.'/auth.php';

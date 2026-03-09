<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VehicleRequestController;
use Illuminate\Support\Facades\Route;

// --- Authentication ---
Route::get('/register', fn() => view('auth.register'));
Route::post('/register', [RegisterController::class, 'store']);

// --- Static Pages ---
Route::view('/', 'home')->name('home');
Route::view('/company-profile', 'company-profile')->name('company-profile');
Route::view('/printing-and-publishing', 'printing-and-publishing')->name('printing-and-publishing');
Route::view('/documentation', 'documentation')->name('documentation');
Route::view('/marketing-communications', 'marketing-communications')->name('marketing.communication');
Route::view('/technology', 'technology')->name('technology');
Route::view('/services', 'services')->name('services');
Route::post('/contact-us', [ContactController::class, 'submit'])
    ->name('contact.submit');

// Calendar for Car Schedule
Route::view('/calendar', 'calendar')->middleware('auth');

// --- THE FIX: Blog & Updates ---
// This handles the list AND the single post view via the slug.
Route::get('/updates/{slug?}', [PostController::class, 'index'])->name('updates');

// Redirect the old /posts path to /updates to prevent conflicts
Route::get('/posts', fn() => redirect()->route('updates'));

// This is additional post
Route::patch('/manage-posts/{post}', [PostController::class, 'update'])
    ->name('posts.update');

// This is additional post
Route::middleware('auth')->group(function () {
    Route::view('/calendar', 'calendar');
    Route::get('/vehicle-requests',      [VehicleRequestController::class, 'index']);
    Route::post('/vehicle-requests',     [VehicleRequestController::class, 'store']);
    Route::delete('/vehicle-requests/{id}', [VehicleRequestController::class, 'destroy']);
});

// --- Protected Routes ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // BLOG ACTIONS
    Route::post('/manage-posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/manage-posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // ✅ ADD THESE
    Route::get('/manage-posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/manage-posts/{post}', [PostController::class, 'update'])->name('posts.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/contact-us', function () {
    return view('contact-us');
})->name('contact');

Route::post('/contact-us', [ContactController::class, 'submit'])
    ->name('contact.submit');

require __DIR__.'/auth.php';

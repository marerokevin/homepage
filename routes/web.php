<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VehicleRequestController;
use App\Http\Controllers\ReportController;
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

// --- Contact ---
Route::get('/contact-us', fn() => view('contact-us'))->name('contact');
Route::post('/contact-us', [ContactController::class, 'submit'])->name('contact.submit');

// --- Blog & Updates ---
Route::get('/updates/{slug?}', [PostController::class, 'index'])->name('updates');
Route::get('/posts', fn() => redirect()->route('updates'));
Route::patch('/manage-posts/{post}', [PostController::class, 'update'])->name('posts.update');

// --- Protected Routes ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard & Calendar
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/calendar', fn() => view('calendar'))->name('calendar');

    // Vehicle Requests API
    Route::get('/vehicle-requests',         [VehicleRequestController::class, 'index']);
    Route::post('/vehicle-requests',        [VehicleRequestController::class, 'store']);
    Route::delete('/vehicle-requests/{id}', [VehicleRequestController::class, 'destroy']);

    // Reports (admin only — enforced inside ReportController)
    Route::get('/reports/vehicle',            [ReportController::class, 'index'])->name('reports.vehicle');
    Route::get('/reports/vehicle/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.vehicle.pdf');
    Route::get('/reports/vehicle/export/csv', [ReportController::class, 'exportCsv'])->name('reports.vehicle.csv');

    // Blog Actions
    Route::post('/manage-posts',              [PostController::class, 'store'])->name('posts.store');
    Route::delete('/manage-posts/{post}',     [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/manage-posts/{post}/edit',   [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/manage-posts/{post}',        [PostController::class, 'update']);

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\LeaveController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VehicleRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomBookingController;
use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Route;

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

// routes/web.php or routes/api.php
Route::get('/vehicle-requests/resources', function () {
    return response()->json([
        'vehicles' => \App\Models\Vehicle::all(),
        'drivers'  => \App\Models\Driver::all(),
    ]);
});

// --- Protected Routes ---
Route::middleware(['auth'])->group(function () {

    // Dashboard & Calendar
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/calendar', fn() => view('calendar'))->name('calendar');

    // Admin user management
    Route::get('/admin/users',                               [UserController::class, 'index'])->name('admin.users');
    Route::patch('/admin/users/{user}/toggle-vehicle',       [UserController::class, 'toggleVehicle']);
    Route::patch('/admin/users/{user}/toggle-vehicle-admin', [UserController::class, 'toggleVehicleAdmin']);

    // Leave Routes
    Route::get('/leaves',                  [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create',           [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves',                 [LeaveController::class, 'store'])->name('leaves.store');
    Route::patch('/leaves/{id}/approve',   [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::patch('/leaves/{id}/reject',    [LeaveController::class, 'reject'])->name('leaves.reject');

    // Vehicle Requests — /users must come before /{id}
    Route::get('/vehicle-requests/users',    [VehicleRequestController::class, 'users']);
    Route::get('/vehicle-requests',          [VehicleRequestController::class, 'index']);
    Route::post('/vehicle-requests',         [VehicleRequestController::class, 'store']);
    Route::delete('/vehicle-requests/{id}',  [VehicleRequestController::class, 'destroy']);

    // Room Bookings
    Route::get('/room-bookings',             [RoomBookingController::class, 'index']);
    Route::post('/room-bookings',            [RoomBookingController::class, 'store']);
    Route::delete('/room-bookings/{id}',     [RoomBookingController::class, 'destroy']);

    // OTP — delete confirmation
    Route::post('/otp/send',   [OtpController::class, 'send']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);

    // Reports
    Route::get('/reports/vehicle',            [ReportController::class, 'index'])->name('reports.vehicle');
    Route::get('/reports/vehicle/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.vehicle.pdf');
    Route::get('/reports/vehicle/export/csv', [ReportController::class, 'exportCsv'])->name('reports.vehicle.csv');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Blog Actions
    Route::post('/manage-posts',            [PostController::class, 'store'])->name('posts.store');
    Route::delete('/manage-posts/{post}',   [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/manage-posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/manage-posts/{post}',      [PostController::class, 'update']);

});

require __DIR__.'/auth.php';

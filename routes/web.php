<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\AdminController;
// use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route as FacadeRoute;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre-nosotros', [HomeController::class, 'about'])->name('about');
Route::get('/servicios', [ServicesController::class, 'index'])->name('services');
Route::get('/modelo-supervisado', [\App\Http\Controllers\ModelController::class, 'show'])->name('model.supervised');
Route::get('/contacto', [ContactController::class, 'show'])->name('contact');
Route::post('/contacto', [ContactController::class, 'submit'])->name('contact.submit');
// Página de requerimientos detallada removida según indicación

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['profile.auth'])->group(function () {
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile');
});

// Admin Routes - Requiere autenticación y rol de administrador
Route::middleware(['profile.auth', 'admin'])->group(function () {
    Route::get('/admin/mensajes-contacto', [ContactController::class, 'messages'])->name('contact.messages');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
});

// Password Reset
Route::get('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'requestForm'])->name('password.request');
Route::post('/password/email', [\App\Http\Controllers\PasswordResetController::class, 'sendLink'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'updatePassword'])->name('password.update');

// Dashboard deshabilitado según indicación
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


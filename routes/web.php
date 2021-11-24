<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\NumberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NumberPreferenceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::group(['middleware' =>'auth'], function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resources([
        'users' => UsersController::class,
        'customers' => CustomerController::class,
        'numbers' => NumberController::class,
    ]);

    Route::resource('number-preferences', NumberPreferenceController::class)
        ->only(['store', 'update', 'destroy']);

    // Restoring routes
    foreach (['customer', 'number', 'users', 'number-preference'] as $controller) {
        $endpoint = Str::plural($controller);
        Route::put(
            "$endpoint/{" . Str::of($controller)->studly()->snake()->singular() . '}/restore',
            ['App\\Http\\Controllers\\' . Str::studly($controller) . 'Controller', 'restore']
        )->name("$endpoint.restore");
    }
});

// Reports

Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');

// Images
Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

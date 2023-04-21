<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [DashboardController::class, 'welcome']);

Route::middleware([
    'auth:sanctum', config('jetstream.auth_session'), 'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])
        ->name('dashboard.chart-data');

    Route::post('/tasks/progress', [TasksController::class, 'progressTasks'])->name('tasks.progress');
    Route::resource('/tasks', TasksController::class)->only([
        'store', 'update', 'destroy',
    ]);
});

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function welcome()
    {
        return Inertia::render('Welcome', [
            'phpVersion' => PHP_VERSION,
            'canLogin' => Route::has('login'),
            'laravelVersion' => Application::VERSION,
            'canRegister' => Route::has('register'),
        ]);
    }

    public function dashboard()
    {
        return Inertia::render('Dashboard');
    }
}

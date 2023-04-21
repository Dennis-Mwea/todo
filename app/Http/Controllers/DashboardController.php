<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function welcome(): Response
    {
        return Inertia::render('Welcome');
    }

    public function dashboard(Request $request): Response
    {
        $tasks = Task::with('task')->whereHas('task', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })->with(['task', 'status'])
            ->when($request->query('search'), fn(Builder $query) => $query
                ->where(function (Builder $q) use ($request) {
                    $q->where('name', 'LIKE', $request->query('search'))
                        ->orWhere('name', 'LIKE', $request->query('search'));
                }))->latest()
            ->paginate($request->integer('perPage', 15));

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'statuses' => Status::get(),
        ]);
    }

    public function chartData(): JsonResponse
    {
        return new JsonResponse();
    }
}

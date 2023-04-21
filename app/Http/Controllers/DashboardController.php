<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
        $query = $this->getQuery();
        $tasks = clone($query)->with(['task', 'status'])
            ->when($request->query('search'), fn(Builder $query) => $query
                ->where(function (Builder $q) use ($request) {
                    $q->where('name', 'LIKE', $request->query('search'))
                        ->orWhere('name', 'LIKE', $request->query('search'));
                }))->latest()
            ->paginate($request->integer('perPage', 15));

        $chart = clone($query)->get();
        $chart = $chart->sortBy('status_id')
            ->groupBy('status_id')
            ->map(fn (Collection $values) => $values->count());

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'statuses' => Status::get(),
            'chartData' => $chart->values(),
        ]);
    }

    public function chartData(): JsonResponse
    {
        $tasks = $this->getQuery()->get();
        $tasks = $tasks->sortBy('status_id')->groupBy('status_id')->map(fn (Collection $values) => $values->count());

        return new JsonResponse(['tasks' => $tasks->values()]);
    }

    private function getQuery(): Builder
    {
        return Task::ownedBy(auth()->id());
    }
}

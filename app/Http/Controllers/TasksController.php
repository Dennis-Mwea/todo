<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $tasks = Task::with('task')->whereHas('task', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })->with(['task', 'status'])->latest()->paginate();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'statuses' => Status::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskRequest $request
     * @return RedirectResponse
     */
    public function store(TaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $task = Task::create([
            'name' => $data['name'],
            'due_date' => $data['due_date'],
            'status_id' => $data['status_id'],
            'description' => $data['description'],
        ]);

        $task->task()->create([
            'user_id' => auth()->id(),
            'due_date' => $data['due_date'],
            'status_id' => $data['status_id'],
        ]);

        return to_route('tasks.index')->with('status', 'Task created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
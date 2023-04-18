<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Actions\ConfirmPassword;

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

        flash("New task created.");

        return to_route('tasks.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Task $task
     * @return Response
     */
    public function edit(Task $task): Response
    {
        return Inertia::render('Tasks/Edit', [
            'statuses' => Status::get(),
            'task' => $task->load('task')->append('formatted_due_date'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        $data = $request->validated();

        $task->loadMissing('task');

        $task = tap($task)->update([
            'name' => $data['name'],
            'due_date' => $data['due_date'],
            'status_id' => $data['status_id'],
            'description' => $data['description'],
        ]);

        tap($task->task)->update([
            'due_date' => $data['due_date'],
            'status_id' => $data['status_id'],
        ]);

        flash("Task $task->name has been updated.");

        return to_route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function destroy(Request $request, Task $task)
    {
        $task->loadMissing('task');

        $confirmed = app(ConfirmPassword::class)(
            auth()->guard('web'),
            $request->user(),
            $request->input('password')
        );

        if (!$confirmed || auth()->id() != $task->task->user_id) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $task->delete();

        flash("Task $task->name has been deleted.");

        return back();
    }
}

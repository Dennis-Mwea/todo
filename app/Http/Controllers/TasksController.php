<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Status;
use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Actions\ConfirmPassword;

class TasksController extends Controller
{
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

        return back();
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

        return back();
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

    public function progressTasks(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'tasks' => ['required', 'array'],
            'tasks.*' => ['numeric', 'exists:tasks,id'],
            'status' => ['required', 'numeric', 'exists:statuses,id'],
        ]);

        $status = Status::find($request->integer('status'));

        Task::whereIn('id', $request->input('tasks'))->update([
            'status_id' => $status->id,
        ]);

        UserTask::whereIn('task_id', $request->input('tasks'))->update([
            'status_id' => $status->id,
        ]);

        flash("Selected tasks status have been changed to $status->name.");

        return back();
    }
}

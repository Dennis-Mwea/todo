<?php

namespace App\Models;

use App\Queries\TaskQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'description', 'due_date', 'status_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_due_date'
    ];

    /**
     * Get the status which the task is in.
     *
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get all the tasks for a user.
     *
     * @return HasOne
     */
    public function task(): HasOne
    {
        return $this->hasOne(UserTask::class);
    }

    /**
     * Format due date to Y-m-d
     *
     * @return string
     */
    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date->format('Y-m-d');
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param Builder $query
     * @return TaskQuery
     */
    public function newEloquentBuilder($query): TaskQuery
    {
        return new TaskQuery($query);
    }
}

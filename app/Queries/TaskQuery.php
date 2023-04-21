<?php

namespace App\Queries;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Task
 */
class TaskQuery extends Builder
{
    public function ownedBy(int $id): self
    {
        return $this->whereHas('task', function (Builder $query) use ($id) {
            $query->where('user_id', $id);
        });
    }
}

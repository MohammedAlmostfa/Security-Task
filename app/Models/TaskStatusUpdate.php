<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatusUpdate extends Model
{
    use HasFactory;
    protected $fillable =
        [
            'task_id',
            'task_status',

        ];
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
 * Scope a query to only include tasks created today.
 *
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @return \Illuminate\Database\Eloquent\Builder
 */
    public function scopeTaskData($query)
    {
        return $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                     ->select(['task_status', 'created_at', 'task_id'])
                     ->with(['task' => function ($query) {
                         $query->select(['id', 'title']);
                     }])
                     ->get();
    }


}

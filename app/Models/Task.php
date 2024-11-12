<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Fields that are mass assignable
    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'priority',
        'due_date',
        'assigned_to',
    ];

    // Relationship with comments
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Relationship with attachments
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    // Relationship with task status updates
    public function taskStatusUpdate()
    {
        return $this->hasMany(TaskStatusUpdate::class);
    }


    // Relationship with task dependencies

    public function dependencies()
    {
        return $this->hasMany(TaskDependencies::class, 'task_id');
    }
    // Relationship with dependent tasks

    public function dependents()
    {
        return $this->hasMany(TaskDependencies::class, 'task_depend_on');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    /**
     * Scope for filtering tasks by type.
     *
     * @param mixed $query
     * @param mixed $type
     * @return mixed
     */
    public function scopeType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }
        return $query;
    }

    /**
     * Scope for filtering tasks by status.
     *
     * @param mixed $query
     * @param mixed $status
     * @return mixed
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for filtering tasks by assigned user.
     *
     * @param mixed $query
     * @param mixed $assignedTo
     * @return mixed
     */
    public function scopeAssignedTo($query, $assignedTo)
    {
        if ($assignedTo) {
            return $query->where('assigned_to', $assignedTo);
        }
        return $query;
    }

    /**
     * Scope for filtering tasks by due date.
     *
     * @param mixed $query
     * @param mixed $dueDate
     * @return mixed
     */
    public function scopeDueDate($query, $dueDate)
    {
        if ($dueDate) {
            return $query->whereDate('due_date', $dueDate);
        }
        return $query;
    }

    /**
     * Scope for filtering tasks by priority.
     *
     * @param mixed $query
     * @param mixed $priority
     * @return mixed
     */
    public function scopePriority($query, $priority)
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }
        return $query;
    }

    /**
     * Scope for filtering tasks by dependency.
     *
     * @param mixed $query
     * @param mixed $dependsOnTaskId
     * @return mixed
     */
    public function scopeDependsOn($query, $dependsOnTaskId)
    {
        if ($dependsOnTaskId) {
            return $query->whereHas('dependencies', function ($q) use ($dependsOnTaskId) {
                $q->where('task_depend_on', $dependsOnTaskId);
            });
        }
        return $query;
    }
}

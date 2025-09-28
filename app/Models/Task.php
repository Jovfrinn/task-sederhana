<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['project_id', 'title', 'status', 'assigned_to', 'created_by'];
    public function project() {
        return $this->belongsTo(Project::class);
    }
    public function user() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function user_create() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function task_user()
    {
        return $this->belongsToMany(User::class, 'task_users')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'user_task', 'task_id', 'user_id')
        ->withTimestamps();
}
}

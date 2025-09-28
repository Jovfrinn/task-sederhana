<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectJoined extends Model
{
    protected $table = 'project_joined';
    protected $fillable = ['user_id','project_id', 'joined_at'];
}

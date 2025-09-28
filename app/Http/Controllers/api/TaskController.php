<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index($id)
    {
        $tasks = Task::orderBy('updated_at', 'DESC')->where('project_id', $id)->with('user_create', 'user')->get();

        return response()->json([
            'status' => '200',
            'data' => $tasks
        ], 200);
    }

    public function assignUser(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->assigned_to = $request->user_id;
        $task->save();

        return response()->json([
            'message' => 'Task berhasil di-assign',
            'task' => $task->load('user')
        ]);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
        'title' => 'required|string|max:255',
        'status' => 'required|string',
        ]);

        $task = Task::create([
            'project_id' => $id,
            'title' => $request->title,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);

    }
    public function update(Request $request, string $id)
    {

        $request->validate([
        'title' => 'required|string|max:255',
        'status' => 'required|string',
        ]);

        $tasks = Task::findOrFail($id);
        
        if (Auth::user()->role == 'admin'){
        $tasks->update([
            'title' => $request->title,
            'status' => $request->status,
        ]);

        return response()->json([
        'message' => 'Task berhasil diupdate',
        'data' => $tasks
        ],200);
        }
        if (Auth::id() == $tasks->created_by){
        $tasks->update([
            'title' => $request->title,
            'status' => $request->status,
        ]);

        return response()->json([
        'message' => 'Task berhasil diupdate',
        'data' => $tasks
        ],200);
        } else {
            return response()->json([
                'message' => 'You Are Not the Task Creator'
            ], 404);
        }
    }
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);

            $task->delete();

            return response()->json([
                'message' => 'Task berhasil dihapus'
            ], 200);
    }
}

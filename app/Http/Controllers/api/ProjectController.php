<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectJoined;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = Project::with('joinedUsers')->get()->map(function ($project) use ($user) {
        return [
            'id'          => $project->id,
            'title'       => $project->title,
            'description' => $project->description,
            'start_date'  => $project->start_date,
            'end_date'    => $project->end_date,
            'joined'      => $user ? $project->joinedUsers->contains($user->id) : false,
            ];
        });

        return response()->json([
            'message' => 'OK',
            'data' => $projects
        ],200);
    }
    public function getProjectJoined()
    {
        try {
            $userId = Auth::id();
            $projectJoined = ProjectJoined::where('user_id', $userId)->pluck('project_id');
            $projects = Project::whereIn('id', $projectJoined)->get();

            return response()->json([
                'message' => 'OK',
                'data' => $projects
            ],200);
        } catch (\Exception $e) {
            return response()->json([
            'error' => 'Something went wrong:' . $e->getMessage()
            ],500);
        }
        
    }

    public function join(Request $request, $id)
    {
        $userId = Auth::id();

        // Apakah user sudah join
        $exists = DB::table('project_joined')
            ->where('project_id', $id)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You joined this project'
            ], 400);
        }

        DB::table('project_joined')->insert([
            'project_id' => $id,
            'user_id' => $userId,
            'joined_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Successfully Joined Project'
        ], 201);
    }

    public function joinedUsers($id)
    {
        $project = Project::with('joinedUsers')->findOrFail($id);
        return response()->json($project->joinedUsers);
    }
    public function store(Request $request)
    {
        try{
        $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project
        ], 201);

        } catch (\Exception $e) {
            return response()->json([
            'error' => 'Failed to create project:' . $e->getMessage()
            ],500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
        $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $project = Project::findOrFail($id);

        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
        'message' => 'Project berhasil diupdate',
        'data' => $project
        ],200);

        } catch (\Exception $e) {
            return response()->json([
            'error' => 'Failed to update project:' . $e->getMessage()
            ],500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->delete();

            return response()->json([
                'message' => 'Project deleted  successfully'
                ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Failed to delete project: ' . $e->getMessage()
            ], 500);

        }
    }
        
}

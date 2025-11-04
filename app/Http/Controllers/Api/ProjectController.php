<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Project};
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $r)
    {
        $orgId = $r->query('organization_id');
        return Project::when($orgId, fn($q) => $q->where('organization_id', $orgId))
            ->withCount('issues')
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'key'  => 'required|string|max:12|unique:projects,key',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($data);

        // default board + columns
        $board = $project->boards()->create(['name' => 'Board']);
        $board->columns()->createMany([
            ['name'=>'Backlog','position'=>100],
            ['name'=>'In Progress','position'=>200],
            ['name'=>'Review','position'=>300],
            ['name'=>'Done','position'=>400],
        ]);

        return response()->json($project->load('boards.columns'), 201);
    }

    public function show(Project $project)
    {
        return $project->load('boards.columns');
    }

    public function update(Request $r, Project $project)
    {
        $data = $r->validate([
            'name'=>'sometimes|string|max:255',
            'description'=>'nullable|string',
        ]);
        $project->update($data);
        return $project;
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->noContent();
    }
}
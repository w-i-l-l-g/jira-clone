<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Issue, Project};
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $r)
    {
        $q = Issue::query()->with(['assignee:id,name','reporter:id,name','column:id,name,board_id']);

        if ($pid = $r->query('project_id')) $q->where('project_id', $pid);
        if ($cid = $r->query('column_id'))  $q->where('column_id', $cid);
        if ($p   = $r->query('priority'))   $q->where('priority', $p);
        if ($s   = $r->query('search'))     $q->where('title','like',"%{$s}%");

        return $q->orderByDesc('id')->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'project_id'  => 'required|exists:projects,id',
            'column_id'   => 'nullable|exists:columns,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $project = Project::findOrFail($data['project_id']);
        $key = $project->nextIssueKey();

        $issue = Issue::create(array_merge($data, [
            'reporter_id' => $r->user()->id,
            'key' => $key,
            'priority' => $data['priority'] ?? 'medium',
        ]));

        return response()->json($issue, 201);
    }

    public function show(Issue $issue) { return $issue->load(['assignee:id,name','reporter:id,name']); }

    public function update(Request $r, Issue $issue)
    {
        $data = $r->validate([
            'title'=>'sometimes|string|max:255',
            'description'=>'nullable|string',
            'priority'=>'in:low,medium,high,urgent',
            'assignee_id'=>'nullable|exists:users,id',
            'column_id'=>'nullable|exists:columns,id',
        ]);
        $issue->update($data);
        return $issue->refresh();
    }

    public function destroy(Issue $issue) { $issue->delete(); return response()->noContent(); }

    public function move(Request $r, Issue $issue)
    {
        $data = $r->validate(['column_id'=>'required|exists:columns,id']);
        $issue->update(['column_id'=>$data['column_id']]);
        return $issue->refresh();
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Board};
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(Request $r)
    {
        $projectId = $r->query('project_id');
        return Board::when($projectId, fn($q)=>$q->where('project_id', $projectId))
            ->with('columns')
            ->get();
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'project_id'=>'required|exists:projects,id',
            'name'=>'nullable|string|max:255',
        ]);
        $board = Board::create([
            'project_id' => $data['project_id'],
            'name' => $data['name'] ?? 'Board',
        ]);
        return response()->json($board->load('columns'), 201);
    }

    public function show(Board $board) { return $board->load('columns'); }
    public function update(Request $r, Board $board)
    {
        $board->update($r->validate(['name'=>'required|string|max:255']));
        return $board;
    }
}
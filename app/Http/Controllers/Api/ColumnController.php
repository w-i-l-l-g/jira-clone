<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function index(Request $r)
    {
        $boardId = $r->query('board_id');
        return Column::when($boardId, fn($q)=>$q->where('board_id',$boardId))
            ->orderBy('position')
            ->get();
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'board_id' => 'required|exists:boards,id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|integer',
        ]);

        // default position at the end (simple approach)
        $position = $data['position'] ?? (Column::where('board_id',$data['board_id'])->max('position') + 100) ?? 100;

        $col = Column::create([
            'board_id' => $data['board_id'],
            'name' => $data['name'],
            'position' => $position,
        ]);

        return response()->json($col, 201);
    }

    public function update(Request $r, Column $column)
    {
        $data = $r->validate([
            'name'=>'sometimes|string|max:255',
            'position'=>'sometimes|integer',
        ]);
        $column->update($data);
        return $column;
    }

    public function destroy(Column $column)
    {
        $column->delete();
        return response()->noContent();
    }

    public function reorder(Request $r)
    {
        $payload = $r->validate([
            'columns' => 'required|array',
            'columns.*.id' => 'required|exists:columns,id',
            'columns.*.position' => 'required|integer',
        ]);
        foreach ($payload['columns'] as $c) {
            Column::whereKey($c['id'])->update(['position' => $c['position']]);
        }
        return response()->json(['ok'=>true]);
    }
}
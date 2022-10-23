<?php

namespace App\Http\Controllers\api;

use App\Models\Column;
use App\Models\Mention;
use Illuminate\Http\Request;
use App\Events\ResourceUpdateEvent;
use App\Http\Controllers\Controller;

class ColumnsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $columns = Column::where('user_id', $user->id)->with('mentions')->orderBy('order', 'asc')->get();
        return response()->json($columns);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);
        $user = $request->user();
        $column = Column::where('user_id', $user->id)->where('id', $id)->first();

        if(!$column) {
            return response()->json(['error' => 'Column not found'], 404);
        }

        $column->name = $request->name ?? $column->name;
        $column->order = $request->order ?? $column->order;
        $column->save();

        ResourceUpdateEvent::dispatch($user, 'column', 'updated', $column->id);

        return response()->json($column);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $column = Column::where('user_id', $user->id)->where('id', $id)->first();
        
        if(!$column) {
            return response()->json(['error' => 'Column not found'], 404);
        }

        if($column->type == "new_mentions") {
            return response()->json(['error' => 'Sorry But You Cannot delete new mentions column'], 400);
        }

        Mention::where('column_id', $column->id)->delete();
        $column->delete();

        ResourceUpdateEvent::dispatch($user, 'column', 'deleted', $column->id);

        return response()->json(['success' => 'Column deleted']);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $column = Column::where('user_id', $user->id)->where('id', $id)->first();

        if(!$column) {
            return response()->json(['error' => 'Column not found'], 404);
        }

        return response()->json($column);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $column = new Column();
        $column->user_id = $user->id;
        $column->name = $request->name ?? "untitled";
        $column->type = "custom";
        $column->order = Column::where('user_id', $user->id)->count() + 1;
        $column->save();

        ResourceUpdateEvent::dispatch($user, 'column', 'added', $column->id);
        return response()->json($column);
    }
}

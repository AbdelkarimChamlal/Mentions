<?php

namespace App\Http\Controllers\api;

use App\Models\Mention;
use Illuminate\Http\Request;
use App\Events\ResourceUpdateEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\MentionResource;

class MentionsController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        return MentionResource::collection(Mention::where('user_id', $user->id)->orderBy('created_at', 'desc')->get());
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'columnId' => 'required|exists:columns,id',
            'order' => 'required|integer'
        ]);

        $user = $request->user();

        $mention = Mention::where([
            'id' => $id,
            'user_id' => $user->id
        ])->first();

        if(!$mention){
            return response()->json([
                'error' => 'Mention not found'
            ], 404);
        }

        $mention->column_id = $request->columnId;
        $mention->order = $request->order;
        $mention->save();
        ResourceUpdateEvent::dispatch($user, 'mentions', 'updated', $mention->id);

        return MentionResource::make($mention);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $mention = Mention::where([
            'id' => $id,
            'user_id' => $user->id
        ])->first();

        if(!$mention){
            return response()->json([
                'error' => 'Mention not found'
            ], 404);
        }

        $mention->delete();
        
        ResourceUpdateEvent::dispatch($user, 'mentions', 'deleted', $mention->id);

        return response()->json([
            'message' => 'Mention deleted successfully'
        ]);
    }
}

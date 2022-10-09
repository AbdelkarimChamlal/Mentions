<?php

namespace App\Http\Controllers\api;

use App\Models\Mention;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MentionResource;

class MentionsController extends Controller
{

    private $supported_columns = 
    [
        'new',
        'processing',
        'done',
        'archived'
    ];
    
    public function index(Request $request)
    {
        $user = $request->user();
        return MentionResource::collection(Mention::where('user_id', $user->id)->orderBy('created_at', 'desc')->get());
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'column' => 'required|in:' . implode(',', $this->supported_columns),
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

        $mention->column = $request->column;
        $mention->order = $request->order;
        $mention->save();


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

        return response()->json([
            'message' => 'Mention deleted successfully'
        ]);
    }
}

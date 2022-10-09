<?php

namespace App\Http\Controllers\api;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return AccountResource::collection(Account::where('user_id', $user->id)->get());
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $account = Account::where([
            'id' => $id,
            'user_id' => $user->id
        ])->first();

        if(!$account){
            return response()->json([
                'error' => 'Account not found'
            ], 404);
        }

        $account->delete();
        return response()->json([
            'message' => "Account deleted"
        ]);
    }
}

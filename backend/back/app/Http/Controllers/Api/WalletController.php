<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    
    public function store(Request $request)
    {

        $validated = $request->validate([
            'amount' => 'required|numeric|min:500|max:100000'
        ]);

        $user = auth()->user();

        $user->increment('balance', $validated['amount']);

        return response()->json([
            'message' => 'Balance successfully topped up',
            'balance' => $user->fresh()->balance
        ], 200);

    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChargingSession;
use App\Models\Connector;

class ChargingSessionController extends Controller
{
    
    public function start(Request $request)
    {

        $data = $request->validate(['qr_code' => 'required|string']);
        $user = auth()->user();

        if ($user->balance < 500) {
            return response()->json([
                'message' => 'Dont have enough balance. Minimum 500 KZT.'
            ], 402);
        };

        if (ChargingSession::where('user_id', $user->id)->where('status', 'charging')->exists()) {
            return response()->json([
                'message' => 'You already have active session'
            ], 400);
        };

        $connector = Connector::where('qr_code', $data['qr_code'])->with('station')->first();

        if (!$connector || $connector->status !== 'available' || $connector->station->status !== 'active') {
            return response()->json(['message' => 'Connector unavailable'], 422);
        };

        $session = DB::transaction(function () use ($user, $connector) {
            $connector->update(['status' => 'busy']);

            return ChargingSession::create([
                'user_id'      => $user->id,
                'connector_id' => $connector->id,
                'start_time'   => now(),
                'status'       => 'charging',
            ]);
        });

        return response()->json([
            'message' => 'Charging started',
            'session_id' => $session->id,
        ]. 201);

    }

    
    public function stop(Request $request, $id)
    {

        $data = $request->validate(['kwh_consumed' => 'required|numeric|min:0.01']);
        $user = auth()->user();

        $session = chargingSession::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'charging')
            ->first();

        if (!$session) {
            return response()->json([
                'message' => 'Active session not found'
            ], 404);
        };

        $station = $session->connector->station;
        $hour = now()->hour;
        $tariff = ($hour >= 18 && $hour < 23) ? $station->price_peak_per_kwh : $station->price_offpeak_per_kwh;
        $totalCost = round($data['kwh_consumed'] * $tariff, 2);

        DB::transaction(function () use ($session, $user, $data, $totalCost) {
            $session->update([
                'end_time' => now(),
                'kwh_consumed' => $data['kwh_consumed'],
                'total_cost' => $totalCost,
                'status' => 'completed'
            ]);

            $session->connector->update(['status' => 'available']);
            $user->decrement('balance', $totalCost);
        });

        return response()->json([
            'message' => 'Charging stopped',
            'total_cost' => $totalCost,
            'new_balance' => $user->fresh()->balance
        ], 200);

    }

}

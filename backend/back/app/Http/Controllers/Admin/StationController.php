<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Station;

class StationController extends Controller
{
    
    public function index()
    {

        return response()->json(Station::all());

    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price_peak_per_kwh' => 'required|numeric|min:0',
            'price_offpeak_per_kwh' => 'required|numeric|min:0',
            'status' => 'required|in:active.inactive.maintenance',
        ]);

        $station = Station::create($data);

        return response()->json([
            'message' => 'Station created successfully',
            'station' => $station
        ], 201);

    }

    public function show($id)
    {

        $station = Station::find($id);

        if (!$station) {
            return response()->json([
                'message' => 'Station not found'
            ], 404);
        };

        return response()->json([
            'station' => $station
        ], 200);

    }

    public function update(Request $request, $id)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price_peak_per_kwh' => 'required|numeric|min:0',
            'price_offpeak_per_kwh' => 'required|numeric|min:0',
            'status' => 'required|in:active.inactive.maintenance',
        ]);

        $station = Station::find($id);

        if (!$station) {
            return response()->json([
                'message' => 'Station not found'
            ], 404);
        };

        $station->update($data);

        return response()->json([
            'message' => 'Station updated successfully',
            'station' => $station
        ], 200);

    }

    public function destroy($id)
    {

        $station = Station::find($id);

        if (!$station) {
            return response()->json([
                'message' => 'Station not found'
            ], 404);
        };

        $station->delete();

        return response()->json([
            'message' => 'Station deleted successfully'
        ], 200);

    }

    public function addConnector(Request $request, $id)
    {

        $station = Station::findOrFail($id);

        if (!$station) {
            return response()->json([
                'message' => 'Station not found'
            ], 404);
        };

        $data = $request->validate([
            'type' => 'required|in:CCS2, CHAdeMO, Type2',
            'power_kw' => 'required|numeric|min:1',
            'qr_code' => 'required|string|unique:connectors,qr_code'
        ]);

        $connector = $station->connectors()->create([
            $data,
            'status' => 'available'
        ]);

        return response()->json([
            'message' => 'Connector added successfully',
            'data' => '$connector'
        ], 201);

    }

}

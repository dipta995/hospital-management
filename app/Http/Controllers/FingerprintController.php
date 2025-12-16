<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FingerprintController extends Controller
{
    /**
     * Store fingerprint data in cache
     * Route: POST /fingerprint-send
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'finger_id'  => 'required|integer',
            'confidence' => 'required|integer',
        ]);

        // Store in cache for 5 minutes
        Cache::put('fingerprint_temp_'.$data['finger_id'], [
            'finger_id'  => $data['finger_id'],
            'confidence' => $data['confidence'],
            'time'       => now(),
        ], now()->addMinutes(5));

        return response()->json([
            'status'  => true,
            'message' => 'Fingerprint received and stored in cache',
            'data'    => $data
        ]);
    }

    /**
     * Show all fingerprint data in cache
     * Route: GET /fingerprint-show
     */
    public function show()
    {
        $fingerprints = [];
        for ($i=1; $i<=127; $i++) { // Maximum 127 fingerprints
            if (Cache::has('fingerprint_temp_'.$i)) {
                $fingerprints[$i] = Cache::get('fingerprint_temp_'.$i);
            }
        }

        if(empty($fingerprints)){
            return response()->json([
                'status' => false,
                'message' => 'No fingerprint data found in cache'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $fingerprints
        ]);
    }

    /**
     * Check if a fingerprint exists in cache
     * Route: GET /fingerprint-check?finger_id=1
     */
    public function check(Request $request)
    {
        $request->validate([
            'finger_id' => 'required|integer',
        ]);

        $fingerID = $request->finger_id;

        if(Employee::where('rfid',$fingerID)->first()){

            return response()->json([
                'status'  => true,
                'message' => 'Fingerprint found',
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Fingerprint not found '
        ], 404);
    }
}

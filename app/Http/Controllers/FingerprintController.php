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
        $employee = Employee::where('rfid', $fingerID)->first();

        if ($employee) {
            // Attendance logic
            $today = now()->toDateString();
            $now = now();

            $attendance = \App\Models\Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if (!$attendance) {
                // First IN of the day
                $attendance = \App\Models\Attendance::create([
                    'employee_id' => $employee->id,
                    'fingerprint_data' => $fingerID,
                    'date' => $today,
                    'in_time' => $now,
                    'out_time' => null,
                ]);
                $message = 'Attendance IN marked.';
            } else {
                // Update OUT time
                $attendance->out_time = $now;
                $attendance->save();
                $message = 'Attendance OUT updated.';
            }

            return response()->json([
                'status'  => true,
                'message' => 'Fingerprint found. ' . $message,
                'attendance' => $attendance
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Fingerprint not found '
        ], 404);
    }
}

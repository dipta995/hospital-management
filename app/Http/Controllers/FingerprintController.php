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
        'status'     => 'required|string|in:new,old,scan',
    ]);

    // Decide text based on status
    $text = 'New fingerprint received';
    if ($data['status'] === 'old') {
        $text = 'Existing fingerprint detected';
    } elseif ($data['status'] === 'scan') {
        $text = 'Fingerprint scanned';
    }

    $record = [
        'text'       => $text,
        'finger_id'  => $data['finger_id'],
        'confidence' => $data['confidence'],
        'status'     => $data['status'],
        'date'       => now()->toDateString(),
        'time'       => now()->toTimeString(),
    ];

    $filePath = base_path('fingure.txt');
    file_put_contents($filePath, json_encode($record));

    return response()->json([
        'status' => true,
            'message' => 'Fingerprint received and stored in file',
            'data'    => $record
        ]);
    }

    /**
     * Show all fingerprint data in cache
     * Route: GET /fingerprint-show
     */
    public function show()
    {
        $filePath = base_path('fingure.txt');
        if (!file_exists($filePath)) {
            return response()->json([
                'status' => false,
                'message' => 'No fingerprint data found in file'
            ], 404);
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        if (empty($data) || !is_array($data)) {
            // Fallback default object with status
            $data = [
                'text' => 'No fingerprint yet.',
                'finger_id' => null,
                'confidence' => null,
                'status' => 'none',
                'date' => null,
                'time' => null
            ];
        }
        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     * Check if a fingerprint exists in cache
     * Route: GET /fingerprint-check?finger_id=1
     */
    public function check(Request $request)
    {
        // Explicitly check for finger_id in request
        if (!$request->has('finger_id') || empty($request->finger_id)) {
            return response()->json([
                'status' => false,
                'message' => 'finger_id not provided'
            ], 404);
        }

        $request->validate([
            'finger_id' => 'required|integer',
        ]);

        $fingerID = $request->finger_id;
        $employee = Employee::where('rfid', $fingerID)->first();

        if ($employee) {
            $today = now()->setTimezone('Asia/Dhaka')->toDateString();
            $dhakaNow = now()->setTimezone('Asia/Dhaka')->format('H:i:s');

            $attendance = \App\Models\Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if ($attendance) {
                // Update only out_time
                $attendance->out_time = $dhakaNow;
                $attendance->save();
                $message = 'Attendance OUT updated.';
            } else {
                // Create new attendance with in_time
                $attendance = \App\Models\Attendance::create([
                    'employee_id' => $employee->id,
                    'fingerprint_data' => $fingerID,
                    'date' => $today,
                    'in_time' => $dhakaNow,
                    'out_time' => null,
                ]);
                $message = 'Attendance IN marked.';
            }

            return response()->json([
                'status'  => true,
                'message' => 'Fingerprint found. ' . $message,
                'attendance' => $attendance
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Fingerprint not found'
        ], 404);
    }
}

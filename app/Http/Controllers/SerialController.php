<?php

namespace App\Http\Controllers;

use App\Models\DoctorRoom;
use App\Models\DoctorSerial;
use App\Models\Reefer;
use App\Models\SerialTrigger;
use Illuminate\Http\Request;

class SerialController extends Controller
{
    public function index($uniqueCode)
    {
        if ($data = DoctorRoom::where('secret_unique_code', $uniqueCode)->first()) {
            $reeferId = $data->reefer_id;
            $branchId=$data->branch_id;
        return view('index',compact('reeferId','branchId','uniqueCode'));
        }else{
            return redirect()->route('admin.home');
        }
    }
    public function indexPublic($uniqueCode)
    {
        if ($data = DoctorRoom::where('secret_unique_code', $uniqueCode)->first()) {
            $reeferId = $data->reefer_id;
            $branchId=$data->branch_id;
        return view('public-view',compact('reeferId','branchId','uniqueCode'));
        }else{
            return redirect()->route('admin.home');
        }
    }

    public function serialLists($reeferId, $branchId)
    {
        // Get the latest one from status 1 (Processing)
        $date = now()->setTimezone('Asia/Dhaka')->toDateString();

        $latestOne = DoctorSerial::where('branch_id', $branchId)
            ->where('status',DoctorSerial::$statusArray[1]) // Ensure only "Processing"
            ->where('reefer_id', $reeferId)
        ->whereDate('date', $date)
            ->orderByRaw('CAST(serial_number AS UNSIGNED) DESC') // Numeric sorting
            ->first();

// Get the next 9 from status 0 (Pending) in ascending order
        $remainingNine = DoctorSerial::where('branch_id', $branchId)
            ->where('status',DoctorSerial::$statusArray[0]) // Ensure only "Pending"
            ->where('reefer_id', $reeferId)
            ->whereDate('date', $date)
            ->orderByRaw('CAST(serial_number AS UNSIGNED) ASC') // Numeric sorting
            ->limit(9)
            ->get();

// Combine both results
        $serials = collect($latestOne ? [$latestOne] : [])->merge($remainingNine);

        // Fetch available room
        $availableRoom = DoctorRoom::where('branch_id', $branchId)
            ->where('reefer_id', $reeferId)
            ->first(['room_no']);

        return response()->json([
            'serials' => $serials,
            'available_room' => $availableRoom->room_no
        ]);
    }



    public function updateSerialStatus(Request $request)
{

    $serial = DoctorSerial::find($request->id);
    if (!$serial) {
        return response()->json(['message' => 'Serial not found'], 404);
    }

    $serial->status = $request->status;
    $serial->save();
    DoctorSerial::where('reefer_id', $serial->reefer_id)
        ->where('id', '!=', $serial->id)
        ->where('status',  DoctorSerial::$statusArray[1])
        ->update(['status' => DoctorSerial::$statusArray[2]]);
    return response()->json(['message' => 'Status updated successfully']);
}





}

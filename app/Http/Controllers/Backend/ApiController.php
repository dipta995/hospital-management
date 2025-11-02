<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Attendence;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Reefer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getProducts(Request $request)
    {
        $query = $request->get('query');
        $branchId = auth()->user()->branch_id;

        $products = Product::where('branch_id', $branchId) // Always filter by branch
        ->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%')
                ->orWhere('code', 'LIKE', '%' . $query . '%');
        })
            ->get(['name', 'price', 'reefer_fee', 'id as productID']);

        return response()->json($products);
    }
    public function getServices(Request $request)
    {
        $query = $request->get('query');
        $branchId = auth()->user()->branch_id;

        $services = Service::where('branch_id', $branchId) // Always filter by branch
        ->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%');
        })
            ->get(['name', 'price', 'id as serviceID']);

        return response()->json($services);
    }

    public function getDoctors(Request $request)
    {
        $query = $request->get('query');
        $products = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('type',Reefer::$typeArray[0])
            ->where('name', 'LIKE', '%' . $query . '%')->get(['name','id as referID']);
        return response()->json($products);
    }

    public function getReefs(Request $request)
    {
        $query = $request->get('query');
        $products = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('name', 'LIKE', '%' . $query . '%')->get(['name','id as referID']);
        return response()->json($products);
    }  public function searchUserPhone(Request $request)
    {
        $query = $request->get('query');
        $products = User::where('phone', 'LIKE', '%' . $query . '%')->get(['name','id as userId','phone']);
        return response()->json($products);
    }

    public function attendanceStore(Request $request, $branch_id, $rfid)
    {
        // Find employee by RFID
        $employee = Employee::where('branch_id', $branch_id)
            ->where('rfid', $rfid)->first();

        if ($employee) {
            // Create a new Attendance record
            $att = new Attendence();
            $att->employee_id = $employee->id;
            $att->branch_id = $branch_id;

            // Set date and time in Asia/Dhaka timezone
            $now = Carbon::now('Asia/Dhaka');
            $att->date = $now->toDateString(); // Format: YYYY-MM-DD
            $att->time = $now->format('h:i A'); // Format: HH:MM AM/PM

            // Save attendance record
            $att->save();

            // Return a success response (1) to ESP
            return response('1', 200)->header('Content-Type', 'text/plain');
        } else {
            // Return a failure response (0) to ESP
            return response('0', 404)->header('Content-Type', 'text/plain');
        }
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PharmacyProduct;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\Reefer;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            ->get(['name', 'price', 'reefer_fee', 'category_id', 'id as productID']);

        return response()->json($products);
    }

    public function getProductParameters($id)
    {
        $product = Product::with('parameters')
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!$product) {
            return response()->json([]);
        }

        return response()->json($product->parameters);
    }

    public function getPharmacyProducts(Request $request)
    {
        $query = $request->get('query');
        $branchId = auth()->user()->branch_id;

        $products = PharmacyProduct::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                    ->orWhere('generic_name', 'LIKE', '%' . $query . '%')
                    ->orWhere('barcode', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'generic_name',
                'strength',
                'sell_price',
            ]);

        $stockMap = PharmacyProduct::stockMapForBranch($branchId);

        $products = $products->map(function ($product) use ($stockMap) {
            $product->current_stock = (float) ($stockMap[$product->id] ?? 0);

            return $product;
        });

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
            ->where('type', Reefer::$typeArray[0])
            ->where('name', 'LIKE', '%' . $query . '%')->get(['name', 'id as referID']);
        return response()->json($products);
    }

    public function getReefs(Request $request)
    {
        $query = $request->get('query');
        $reefers = Reefer::with('customParcent')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->get();

        return response()->json($reefers->map(function (Reefer $refer) {
            return [
                'name' => $refer->name,
                'referID' => $refer->id,
                'percent' => (float) $refer->percent,
                'has_custom_percent' => $refer->customParcent->isNotEmpty(),
                'custom_percents' => $refer->customParcent
                    ->pluck('percentage', 'category_id')
                    ->map(fn ($value) => (float) $value)
                    ->all(),
            ];
        }));
    }

    public function searchUserPhone(Request $request)
    {
        $query = $request->get('query');
        $products = User::where(function ($q) use ($query) {
            $q->where('phone', 'LIKE', '%' . $query . '%')
                ->orWhere('name', 'LIKE', '%' . $query . '%');
        })->get([
            'name',
            'id as userId',
            'phone',
            'email',
            'age',
            'gender',
            'blood_group',
            'address',
        ]);
        return response()->json($products);
    }

    public function attendanceStore(Request $request, $branch_id, $rfid)
    {
        // Find employee by RFID
        $employee = Employee::where('branch_id', $branch_id)
            ->where('rfid', $rfid)->first();

        if ($employee) {
            $now = \Carbon\Carbon::now('Asia/Dhaka');
            $date = $now->toDateString();
            $mode = Setting::getByBranch($employee->branch_id, 'attendance_mode', 'standard');
            $isHourly = $mode === 'hourly';

            $openAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $date)
                ->where('mode', $isHourly ? 'hourly' : 'standard')
                ->whereNull('out_time')
                ->orderByDesc('id')
                ->first();

            if ($openAttendance) {
                $openAttendance->out_time = $now;
                $openAttendance->save();
            } else {
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'fingerprint_data' => (string) $rfid,
                    'mode' => $isHourly ? 'hourly' : 'standard',
                    'hour_slot' => (int) $now->format('G'),
                    'date' => $date,
                    'in_time' => $now,
                    'out_time' => null,
                ]);
            }

            // Return a success response (1) to ESP
            return response('1', 200)->header('Content-Type', 'text/plain');
        } else {
            // Return a failure response (0) to ESP
            return response('0', 404)->header('Content-Type', 'text/plain');
        }
    }

    public function storeUser(Request $request)
    {
//        return $request;
        $rules = [
            'name' => 'required|string|max:50',
            'phone' => 'required|digits:11',
            'age' => 'required',
            'address' => 'required|string|max:255',
//            'password' => 'required|min:8|confirmed',
        ];
        $request->validate($rules);

        $user = new User();
        $user->name = $request->name;
//            $user->email = $request->name.'1@email.com';
        $user->phone = $request->phone;
        $user->age = $request->age;
        $user->gender = $request->gender;
        $user->blood_group = $request->blood_group;
        $user->address = $request->address;
        $user->password = Hash::make(12345678);

        if ($user->save()) {
            return response()->json(
                [
                    'id' => $user->id,
                    'customer_name' => $user->name,
                ]
            );

        } else {
            return response()->json(['error' => 400]);
        }


    }

    public function getByCategory($id)
    {
        $services = Service::where('service_category_id', $id)->select('id', 'name', 'price')->get();
        return response()->json($services);
    }

}

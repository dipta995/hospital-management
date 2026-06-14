<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\DoctorSerial;
use App\Models\Reefer;
use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DoctorSerialController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.doctor_serials.index";
    public $create_route = "admin.doctor_serials.create";
    public $store_route = "admin.doctor_serials.store";
    public $edit_route = "admin.doctor_serials.edit";
    public $update_route = "admin.doctor_serials.update";

    public function __construct()
    {
        $this->checkGuard();

        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Doctor Serials",
            'sub_title' => "",
            'plural_name' => "doctor_serials",
            'singular_name' => "DoctorSerial",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/doctor-serials'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('doctor_serials.index');

        $date = request()->filled('date')
            ? request()->input('date')
            : now()->setTimezone('Asia/Dhaka')->toDateString();

        $data['pageHeader'] = $this->pageHeader;
        $data['reefers'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('type', Reefer::$typeArray[0])
            ->orderBy('name')
            ->get(['id', 'name']);
        $data['selectedDate'] = $date;
        $data['selectedReeferId'] = request('reefer_id');

        $query = DoctorSerial::with('doctor')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereDate('date', $date);

        if (request()->filled('reefer_id')) {
            $query->where('reefer_id', request('reefer_id'));
        }

        $query->orderBy('reefer_id')
            ->orderByRaw('CAST(serial_number AS UNSIGNED) ASC');

        if (request()->input('export') == 'pdf') {
            $data['datas'] = $query->get();
            return view('backend.pages.doctor_serials.pdf-serial', $data);
        }

        $data['datas'] = $query->get();
        $data['totalSerials'] = $data['datas']->count();
        $data['pendingCount'] = $data['datas']->where('status', DoctorSerial::$statusArray[0])->count();

        return view('backend.pages.doctor_serials.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('doctor_serials.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['reefers'] = Reefer::where('branch_id',auth()->user()->branch_id)
            ->where('type',Reefer::$typeArray[0])->get();

        return view('backend.pages.doctor_serials.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//return $request;
        $this->checkOwnPermission('doctor_serials.create');
        $rules = [
            'reefer_id' => 'required',
            'patient_name' => 'required',
            'patient_phone' => ['nullable', 'regex:/^01[0-9]{9}$/'],
        ];
        $request->validate($rules);
        $dr = Reefer::find($request->reefer_id);
        if($dr->office_time == null){
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!! </strong> রেফার লিস্ট থেকে ডাক্তার এডিট করার সময় রোগী দেখার সময় নির্ধারণ করে তা অবশ্যই সংরক্ষণ করতে হবে। এটি না দিলে ফর্ম সাবমিট করা যাবে না।.');
        }
        try {
            if (!DoctorSerial::where('date',$request->date)->where('branch_id',auth()->user()->branch_id)
                ->where('serial_number',$request->serial_number)
                ->where('reefer_id',$request->reefer_id)->first()){
            $row = new DoctorSerial();
            $row->branch_id = auth()->user()->branch_id;
            $row->reefer_id = $request->reefer_id;
            $row->patient_name = $request->patient_name;
            $row->patient_age_year = $request->patient_age_year;
            $row->patient_phone = $request->patient_phone;
            $row->patient_email = $request->patient_email;
            $row->patient_gender = $request->patient_gender;
            $row->patient_blood_group = $request->patient_blood_group;
            $row->patient_address = $request->patient_address;
            $row->serial_number = $request->serial_number;  //self::generateSerialNumber($request->reefer_id);
            $row->amount = $request->amount;
            $row->date = $request->date;
            $row->remarks = $request->remarks;
            $row->status = DoctorSerial::$statusArray[0];

            if ($row->save()) {
                if($request->send_sms=='yes') {
                if(Setting::get('doctors_appointment')=='Yes') {
                    $startTime = Carbon::parse($dr->office_time);
                    $serial = $row->serial_number;
                    $minutesToAdd = ($serial - 1) * 3;
                    $approxTime = $startTime->copy()->addMinutes($minutesToAdd);
                    $format = Setting::get('doctors_appointment_sms_format');
                    $message = str_replace(
                        ['{patient_name}', '{serial}', '{date}', '{time}','{doctor}'],
                        [
                            $request->patient_name,
                            $serial,
                            \Carbon\Carbon::parse($request->date)->format('d/m/y'),
                            $approxTime->format('g:i A'),
                            $dr->name
                        ],
                        $format
                    );
                    smsSent(auth()->user()->branch_id, $request->patient_phone, $message);
                }
                }
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> DoctorSerial Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
            }else{
                return RedirectHelper::backWithInputFromException("<strong>Sorry!!! </strong> Serial Number Already Exists.");
            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

    }
    public function generateSerialNumber($reeferId, $forDate = null)
    {
        $currentDate = $forDate ? Carbon::parse($forDate)->toDateString() : Carbon::now('Asia/Dhaka')->toDateString();

        $maxSerial = DoctorSerial::where('branch_id', auth()->user()->branch_id)
            ->where('reefer_id', $reeferId)
            ->whereDate('date', $currentDate)
            ->max(DB::raw('CAST(serial_number AS UNSIGNED)'));

        return ((int) $maxSerial) + 1;
    }

    /**
     * Return next serial and approximate time for a given doctor (reefer) via AJAX.
     */
    public function nextSerialAjax($reeferId)
    {
        $date = request()->query('date');
        $serial = $this->generateSerialNumber($reeferId, $date);
        $dr = Reefer::find($reeferId);

        $approxTime = null;
        if ($dr && $dr->office_time) {
            try {
                $startTime = Carbon::parse($dr->office_time);
                $minutesToAdd = ($serial - 1) * 3; // keeps existing 3 min per serial assumption
                $approxTime = $startTime->copy()->addMinutes($minutesToAdd)->format('g:i A');
            } catch (\Exception $e) {
                $approxTime = null;
            }
        }

        return response()->json([
            'serial' => $serial,
            'approx_time' => $approxTime,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
{
    $this->checkOwnPermission('doctor_serials.edit');
    $data['edited'] = DoctorSerial::with('doctor')
        ->where('branch_id', auth()->user()->branch_id)
        ->find($id);
    $data['pageHeader'] = $this->pageHeader;
    $data['statusOptions'] = DoctorSerial::$statusArray;

    if ($data['edited']) {
        return view('backend.pages.doctor_serials.edit', $data);
    }

    return RedirectHelper::backWithInputFromException();
}



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('doctor_serials.edit');
        $request->validate([
            'patient_name' => 'required|max:200',
            'date' => 'required|date',
            'serial_number' => 'required',
            'status' => 'nullable|in:' . implode(',', DoctorSerial::$statusArray),
            'patient_phone' => ['nullable', 'regex:/^01[0-9]{9}$/'],
        ]);
        try {
            $row = DoctorSerial::where('branch_id', auth()->user()->branch_id)
                ->findOrFail($id);

            $duplicate = DoctorSerial::where('branch_id', auth()->user()->branch_id)
                ->where('reefer_id', $row->reefer_id)
                ->whereDate('date', $request->date)
                ->where('serial_number', $request->serial_number)
                ->where('id', '!=', $id)
                ->exists();

            if ($duplicate) {
                return RedirectHelper::backWithInputFromException('<strong>Sorry!</strong> Serial number already exists for this doctor on this date.');
            }

            $row->patient_name = $request->patient_name;
            $row->patient_age_year = $request->patient_age_year;
            $row->patient_phone = $request->patient_phone;
            $row->patient_email = $request->patient_email;
            $row->patient_gender = $request->patient_gender;
            $row->patient_blood_group = $request->patient_blood_group;
            $row->patient_address = $request->patient_address;
            $row->serial_number = $request->serial_number;
            $row->amount = $request->amount;
            $row->date = $request->date;
            $row->remarks = $request->remarks;
            $row->status = $request->input('status', $row->status);
            $row->save();

            return RedirectHelper::routeSuccessWithParams(
                $this->index_route,
                '<strong>Success!</strong> Doctor serial updated successfully.',
                ['date' => $request->date, 'reefer_id' => $row->reefer_id]
            );

        } catch (QueryException $e) {
            return $e;
         return RedirectHelper::backWithInputFromException();
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        $this->checkOwnPermission('doctor_serials.delete');
        $deleteData = DoctorSerial::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }


}

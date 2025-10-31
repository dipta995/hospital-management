<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\DoctorRoom;
use App\Models\Reefer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;

class DoctorRoomController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.doctor_rooms.index";
    public $create_route = "admin.doctor_rooms.create";
    public $store_route = "admin.doctor_rooms.store";
    public $edit_route = "admin.doctor_rooms.edit";
    public $update_route = "admin.doctor_rooms.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Invoices",
            'sub_title' => "",
            'plural_name' => "doctor_rooms",
            'singular_name' => "DoctorRoom",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/doctor-rooms'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('doctor_rooms.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = DoctorRoom::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.doctor_rooms.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('doctor_rooms.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['reefers'] = Reefer::where('branch_id',auth()->user()->branch_id)
            ->where('type',Reefer::$typeArray[0])->get();

        return view('backend.pages.doctor_rooms.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request;
        $this->checkOwnPermission('doctor_rooms.create');
        $rules = [
            'reefer_id' => 'required',
            'room_no' => 'required',
        ];
        $request->validate($rules);
        try {
            if(!DoctorRoom::where('reefer_id',$request->reefer_id)->first()){
            $row = new DoctorRoom();
            $row->branch_id = auth()->user()->branch_id;
            $row->reefer_id = $request->reefer_id;
            $row->room_no = $request->room_no;
            $randomNumber = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // Ensures 2 digits (00-99)
            $secretCode = str_pad($request->reefer_id, 2, '0', STR_PAD_LEFT) . $randomNumber; // Ensures 4 digits
            $row->secret_unique_code = $secretCode;


            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> DoctorRoom Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
            }else{
                return RedirectHelper::backWithInputFromException('<strong>Sorry!!! </strong> You can not crate multiple.');
            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

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
        $this->checkOwnPermission('doctor_rooms.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = DoctorRoom::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
        return view('backend.pages.doctor_rooms.edit', $data);
        }else{
            return RedirectHelper::backWithInputFromException();
        }
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
        $this->checkOwnPermission('doctor_rooms.edit');

            $request->validate([
                'room_no' => 'required|max:10',
            ]);
            try {
                if($row = DoctorRoom::where('branch_id', auth()->user()->branch_id)
                    ->find($id)){
                    $row->room_no = $request->room_no;
                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> DoctorRoom Created Successfully');

                } else {
                    return RedirectHelper::backWithInput();
                }
                }else{
                    return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

                }
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
        $this->checkOwnPermission('doctor_rooms.delete');
        $deleteData = DoctorRoom::where('branch_id', auth()->user()->branch_id)
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

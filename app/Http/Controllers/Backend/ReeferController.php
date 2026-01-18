<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomPercent;
use App\Models\Reefer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ReeferController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.reefers.index";
    public $create_route = "admin.reefers.create";
    public $store_route = "admin.reefers.store";
    public $edit_route = "admin.reefers.edit";
    public $update_route = "admin.reefers.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Reefers",
            'sub_title' => "",
            'plural_name' => "reefers",
            'singular_name' => "Reefer",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/reefers'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->checkOwnPermission('reefers.index');
        $data['pageHeader'] = $this->pageHeader;
        $query = Reefer::where('branch_id', auth()->user()->branch_id);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $data['datas'] = $query->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.reefers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('reefers.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = Category::where('branch_id', auth()->user()->branch_id)->get();
        return view('backend.pages.reefers.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkOwnPermission('reefers.create');
        $rules = [
            'name' => 'required|max:200',
            'percent' => 'required',
        ];
        $request->validate($rules);
        try {
            DB::beginTransaction();
            $row = new Reefer();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->phone = $request->phone;
            $row->designation = $request->designation;
            $row->percent = $request->percent;
            $row->type = $request->type;
            $row->office_time = $request->office_time;
            $row->save();
//            if ($request->has('custom_percent')=='yes') {
//                foreach ($request->custom_percent as $categoryId => $percent) {
//                    if ($percent !== null) {
//                        CustomPercent::create([
//                            'branch_id' => auth()->user()->branch_id,
//                            'refer_id' => $row->id,
//                            'category_id' => $categoryId,
//                            'percentage' => $percent,
//                        ]);
//                    }
//                }
//            }
            DB::commit();
            return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Reefer Created Successfully');


        } catch (QueryException $e) {
            DB::rollBack();
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

    }

    /**
     * Store a newly created resource via AJAX and return JSON.
     */
    public function storeApi(Request $request)
    {
        $this->checkOwnPermission('reefers.create');

        $rules = [
            'name' => 'required|max:200',
            'percent' => 'required',
        ];
        $request->validate($rules);

        try {
            DB::beginTransaction();

            $row = new Reefer();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->phone = $request->phone;
            $row->designation = $request->designation;
            $row->percent = $request->percent;
            $row->type = $request->type;
            $row->office_time = $request->office_time;
            $row->save();

            DB::commit();

            return response()->json([
                'id' => $row->id,
                'name' => $row->name,
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
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
        $this->checkOwnPermission('reefers.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $data['categories'] = Category::where('branch_id',auth()->user()->branch_id)->get();
            return view('backend.pages.reefers.edit', $data);
        } else {
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
        $this->checkOwnPermission('reefers.edit');

        $request->validate([
            'name' => 'required|max:200',
            'percent' => 'required',
        ]);
        try {
            DB::beginTransaction();
            if ($row = Reefer::where('branch_id', auth()->user()->branch_id)
                ->find($id)) {
                $row->name = $request->name;
                $row->phone = $request->phone;
                $row->designation = $request->designation;
                $row->percent = $request->percent;
                $row->type = $request->type;
                $row->office_time = $request->office_time;

               $row->save();
//                if ($request->has('custom_percent')=='yes') {
//                    foreach ($request->custom_percent as $categoryId => $percent) {
//                        if ($percent !== null) {
//                            CustomPercent::updateOrCreate(
//                                [
//                                    'branch_id' => auth()->user()->branch_id,
//                                    'refer_id' => $row->id,
//                                    'category_id' => $categoryId,
//                                ],
//                                [
//                                    'percentage' => $percent,
//                                ]
//                            );
//                        }
//                    }
//                }

                DB::commit();
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Sorry !!!</strong>Data not found');

            } else {
                return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

            }
        } catch (QueryException $e) {
            DB::rollBack();
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
        $this->checkOwnPermission('reefers.delete');
        $deleteData = Reefer::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }


    public function customSms()
    {
        $this->checkOwnPermission('reefers.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('type', Reefer::$typeArray[1])->orderBy('id', 'DESC')->get();
        return view('backend.pages.reefers.custom-sms', $data);
    }

    public function customSmsSend(Request $request)
    {
        $message = $request->message;
        $ids = explode(',', $request->selected_ids);
        $reefers = Reefer::whereIn('id', $ids)
            ->whereNotNull('phone')
            ->select('id', 'phone')
            ->get();

        foreach ($reefers as $reefer) {
            smsSent(auth()->user()->branch_id, $reefer->phone, $message);
        }

        return RedirectHelper::back('<strong>Congratulations!!!</strong> Sms send Successfully');

    }

}

<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PaymentController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.payments.index";
    public $create_route = "admin.payments.create";
    public $store_route = "admin.payments.store";
    public $edit_route = "admin.payments.edit";
    public $update_route = "admin.payments.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Categories",
            'sub_title' => "",
            'plural_name' => "payments",
            'singular_name' => "Payment",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/payments'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('payments.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Payment::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.payments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('payments.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.payments.create', $data);
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
        $this->checkOwnPermission('payments.create');
        $rules = [
            'name' => 'required|max:200',
        ];
        $request->validate($rules);
        try {
            $row = new Payment();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->room_no = $request->room_no;
            $row->room_name = $request->room_name;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Payment Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
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
        $this->checkOwnPermission('payments.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = Payment::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
        return view('backend.pages.payments.edit', $data);
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
        $this->checkOwnPermission('payments.edit');
            $request->validate([
                'name' => 'required|max:200',
            ]);
            try {
                if($row = Payment::where('branch_id', auth()->user()->branch_id)
                    ->find($id)){
                $row->name = $request->name;
                    $row->room_no = $request->room_no;
                    $row->room_name = $request->room_name;
                    if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Payment Created Successfully');

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
        $this->checkOwnPermission('payments.delete');
        $deleteData = Payment::where('branch_id', auth()->user()->branch_id)
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

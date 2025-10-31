<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\TestReportDemo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class TestReportDemoController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.test_report_demos.index";
    public $create_route = "admin.test_report_demos.create";
    public $store_route = "admin.test_report_demos.store";
    public $edit_route = "admin.test_report_demos.edit";
    public $update_route = "admin.test_report_demos.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Test Reports",
            'sub_title' => "",
            'plural_name' => "test_report_demos",
            'singular_name' => "TestReportDemo",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/test_report_demos'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('test_report_demos.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = TestReportDemo::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.test_report_demos.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('test_report_demos.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.test_report_demos.create', $data);
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
        $this->checkOwnPermission('test_report_demos.create');
        $rules = [
            'name' => 'required|max:200',
            'test_report' => 'required',
        ];
        $request->validate($rules);
        try {
            $row = new TestReportDemo();
            $row->name = $request->name;
            $row->test_report = $request->test_report;
            $row->type = $request->type;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> TestReportDemo Created Successfully');

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
        $this->checkOwnPermission('test_report_demos.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = TestReportDemo::find($id)) {
        return view('backend.pages.test_report_demos.edit', $data);
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
        $this->checkOwnPermission('test_report_demos.edit');
            $request->validate([
                'name' => 'required|max:200',
                'test_report' => 'required',
            ]);
            try {
                if($row = TestReportDemo::find($id)){
                    $row->name = $request->name;
                    $row->test_report = $request->test_report;
                    $row->type = $request->type;
                    if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> TestReportDemo Created Successfully');

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
        $this->checkOwnPermission('test_report_demos.delete');
        $deleteData = TestReportDemo::where('branch_id', auth()->user()->branch_id)
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

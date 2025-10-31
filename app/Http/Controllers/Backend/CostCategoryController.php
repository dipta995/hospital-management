<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\CostCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class CostCategoryController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.cost_categories.index";
    public $create_route = "admin.cost_categories.create";
    public $store_route = "admin.cost_categories.store";
    public $edit_route = "admin.cost_categories.edit";
    public $update_route = "admin.cost_categories.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Cost Categories",
            'sub_title' => "",
            'plural_name' => "cost_categories",
            'singular_name' => "CostCategory",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/cost-categories'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('cost_categories.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = CostCategory::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.cost_categories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('cost_categories.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.cost_categories.create', $data);
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
        $this->checkOwnPermission('cost_categories.create');
        $rules = [
            'name' => 'required|max:200',
        ];
        $request->validate($rules);
        try {
            $row = new CostCategory();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> CostCategory Created Successfully');

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
        $this->checkOwnPermission('cost_categories.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = CostCategory::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
        return view('backend.pages.cost_categories.edit', $data);
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
        $this->checkOwnPermission('cost_categories.edit');
            $request->validate([
                'name' => 'required|max:200',
            ]);
            try {
                if($row = CostCategory::where('branch_id', auth()->user()->branch_id)
                    ->find($id)){
                $row->name = $request->name;
                    if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> CostCategory Created Successfully');

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
        $this->checkOwnPermission('cost_categories.delete');
        $deleteData = CostCategory::where('branch_id', auth()->user()->branch_id)
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

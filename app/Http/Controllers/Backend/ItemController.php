<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ItemController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.items.index";
    public $create_route = "admin.items.create";
    public $store_route = "admin.items.store";
    public $edit_route = "admin.items.edit";
    public $update_route = "admin.items.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Categories",
            'sub_title' => "",
            'plural_name' => "items",
            'singular_name' => "Item",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/items'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('items.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Item::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.items.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('items.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.items.create', $data);
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
        $this->checkOwnPermission('items.create');
        $rules = [
            'name' => 'required|string|max:200',
            'code' => 'required|integer|min:1',
        ];
        $request->validate($rules);
        try {
            $row = new Item();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->code = $request->code;
            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Item Created Successfully');
            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
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
        $this->checkOwnPermission('items.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = Item::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
        return view('backend.pages.items.edit', $data);
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
        $this->checkOwnPermission('items.edit');
            $request->validate([
                'name' => 'required|string|max:200',
                'code' => 'required|integer|min:1',
            ]);
            try {
                if($row = Item::where('branch_id', auth()->user()->branch_id)
                    ->find($id)){
                    $row->name = $request->name;
                    $row->code = $request->code;
                    if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Item Created Successfully');
                } else {
                    return RedirectHelper::backWithInput();
                }
                }else{
                    return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

                }
            } catch (QueryException $e) {
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
        $this->checkOwnPermission('items.delete');
        $deleteData = Item::where('branch_id', auth()->user()->branch_id)
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

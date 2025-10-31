<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class SupplierController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.suppliers.index";
    public $create_route = "admin.suppliers.create";
    public $store_route = "admin.suppliers.store";
    public $edit_route = "admin.suppliers.edit";
    public $update_route = "admin.suppliers.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Suppliers",
            'sub_title' => "",
            'plural_name' => "suppliers",
            'singular_name' => "Supplier",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/suppliers'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('suppliers.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Supplier::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.suppliers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('suppliers.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.suppliers.create', $data);
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
        $this->checkOwnPermission('suppliers.create');
        $rules = [
            'name' => 'required|max:200',
            'contact_person' => 'required|max:200',
            'phone' => 'required|max:200',
            'email' => 'required|max:200',
            'address' => 'required|max:200',
        ];
        $request->validate($rules);
        try {
            $row = new Supplier();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->contact_person = $request->contact_person;
            $row->phone = $request->phone;
            $row->email = $request->email;
            $row->address = $request->address;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Supplier Created Successfully');

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
        $this->checkOwnPermission('suppliers.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = Supplier::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
        return view('backend.pages.suppliers.edit', $data);
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
        $this->checkOwnPermission('suppliers.edit');
            $request->validate([
                'name' => 'required|max:200',
                'contact_person' => 'required|max:200',
                'phone' => 'required|max:200',
                'email' => 'required|max:200',
                'address' => 'required|max:200',
            ]);
            try {
                if($row = Supplier::where('branch_id', auth()->user()->branch_id)
                    ->find($id)){
                    $row->name = $request->name;
                    $row->contact_person = $request->contact_person;
                    $row->phone = $request->phone;
                    $row->email = $request->email;
                    $row->address = $request->address;
                    if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Supplier Created Successfully');

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
        $this->checkOwnPermission('suppliers.delete');
        $deleteData = Supplier::where('branch_id', auth()->user()->branch_id)
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

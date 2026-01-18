<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\PharmacyType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PharmacyTypeController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.pharmacy_types.index";
    public $create_route = "admin.pharmacy_types.create";
    public $store_route = "admin.pharmacy_types.store";
    public $edit_route = "admin.pharmacy_types.edit";
    public $update_route = "admin.pharmacy_types.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Pharmacy Types",
            'sub_title' => "",
            'plural_name' => "pharmacy_types",
            'singular_name' => "PharmacyType",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/pharmacy-types'),

        ];
    }

    public function index()
    {
        $this->checkOwnPermission('pharmacy_types.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = PharmacyType::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.pharmacy_types.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('pharmacy_types.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.pharmacy_types.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('pharmacy_types.create');
        $rules = [
            'name' => 'required|max:200',
        ];
        $request->validate($rules);
        try {
            $row = new PharmacyType();
            $row->name = $request->name;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Type Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

    }

    public function edit($id)
    {
        $this->checkOwnPermission('pharmacy_types.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = PharmacyType::find($id)) {
            return view('backend.pages.pharmacy_types.edit', $data);
        } else {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('pharmacy_types.edit');
        $request->validate([
            'name' => 'required|max:200',
        ]);
        try {
            if ($row = PharmacyType::find($id)) {
                $row->name = $request->name;
                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Type Updated Successfully');

                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

    }

    public function destroy($id)
    {
        $this->checkOwnPermission('pharmacy_types.delete');
        $deleteData = PharmacyType::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}

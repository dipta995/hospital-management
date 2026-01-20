<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\PharmacyUnit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PharmacyUnitController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.pharmacy_units.index";
    public $create_route = "admin.pharmacy_units.create";
    public $store_route = "admin.pharmacy_units.store";
    public $edit_route = "admin.pharmacy_units.edit";
    public $update_route = "admin.pharmacy_units.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Pharmacy Units",
            'sub_title' => "",
            'plural_name' => "pharmacy_units",
            'singular_name' => "PharmacyUnit",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/pharmacy-units'),

        ];
    }

    public function index()
    {
        $this->checkOwnPermission('pharmacy_units.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = PharmacyUnit::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.pharmacy_units.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('pharmacy_units.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.pharmacy_units.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('pharmacy_units.create');
        $rules = [
            'name' => 'required|max:200',
        ];
        $request->validate($rules);
        try {
            $row = new PharmacyUnit();
            $row->name = $request->name;
            // Default status to active when creating
            $row->status = 1;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Unit Created Successfully');

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
        $this->checkOwnPermission('pharmacy_units.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = PharmacyUnit::find($id)) {
            return view('backend.pages.pharmacy_units.edit', $data);
        } else {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('pharmacy_units.edit');
        $request->validate([
            'name' => 'required|max:200',
        ]);
        try {
            if ($row = PharmacyUnit::find($id)) {
                $row->name = $request->name;
                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Unit Updated Successfully');

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
        $this->checkOwnPermission('pharmacy_units.delete');
        $deleteData = PharmacyUnit::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}

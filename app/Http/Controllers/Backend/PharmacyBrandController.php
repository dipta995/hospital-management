<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\PharmacyBrand;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PharmacyBrandController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.pharmacy_brands.index";
    public $create_route = "admin.pharmacy_brands.create";
    public $store_route = "admin.pharmacy_brands.store";
    public $edit_route = "admin.pharmacy_brands.edit";
    public $update_route = "admin.pharmacy_brands.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Pharmacy Brands",
            'sub_title' => "",
            'plural_name' => "pharmacy_brands",
            'singular_name' => "PharmacyBrand",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/pharmacy-brands'),

        ];
    }

    public function index()
    {
        $this->checkOwnPermission('pharmacy_brands.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = PharmacyBrand::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.pharmacy_brands.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('pharmacy_brands.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.pharmacy_brands.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('pharmacy_brands.create');
        $rules = [
            'name' => 'required|max:200',
            'status' => 'required|boolean',
        ];
        $request->validate($rules);
        try {
            $row = new PharmacyBrand();
            $row->name = $request->name;
            $row->status = $request->status;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Brand Created Successfully');

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
        $this->checkOwnPermission('pharmacy_brands.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = PharmacyBrand::find($id)) {
            return view('backend.pages.pharmacy_brands.edit', $data);
        } else {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('pharmacy_brands.edit');
        $request->validate([
            'name' => 'required|max:200',
            'status' => 'required|boolean',
        ]);
        try {
            if ($row = PharmacyBrand::find($id)) {
                $row->name = $request->name;
                $row->status = $request->status;
                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Brand Updated Successfully');

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
        $this->checkOwnPermission('pharmacy_brands.delete');
        $deleteData = PharmacyBrand::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ServiceController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.services.index";
    public $create_route = "admin.services.create";
    public $store_route = "admin.services.store";
    public $edit_route = "admin.services.edit";
    public $update_route = "admin.services.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Services",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/services'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('services.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Service::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.services.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('services.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = ServiceCategory::get();
        return view('backend.pages.services.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('services.create');
        $request->validate([
            'name' => 'required|max:200',
            'service_category_id' => 'required',
            'price' => 'required|numeric',
        ]);

        try {
            $row = new Service();
            $row->branch_id = auth()->user()->branch_id;
            $row->service_category_id = $request->service_category_id;
            $row->name = $request->name;
            $row->price = $request->price;
            $row->note = $request->note;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, 'Service created successfully.');
            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('services.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = ServiceCategory::get();

        if ($data['edited'] = Service::where('branch_id', auth()->user()->branch_id)->find($id)) {
            return view('backend.pages.services.edit', $data);
        } else {
            return RedirectHelper::routeError($this->index_route, 'Service not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('services.edit');
        $request->validate([
            'name' => 'required|max:200',
            'price' => 'required|numeric',
        ]);

        try {
            if ($row = Service::where('branch_id', auth()->user()->branch_id)->find($id)) {
                $row->service_category_id = $request->service_category_id;
                $row->name = $request->name;
                $row->price = $request->price;
                $row->note = $request->note;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Service updated successfully.');
                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, 'Service not found.');
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('services.delete');
        $deleteData = Service::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}

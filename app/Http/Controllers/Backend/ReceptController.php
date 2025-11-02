<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Recept;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ReceptController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.recepts.index";
    public $create_route = "admin.recepts.create";
    public $store_route = "admin.recepts.store";
    public $edit_route = "admin.recepts.edit";
    public $update_route = "admin.recepts.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Recepts",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/recepts'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('recepts.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Recept::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.recepts.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('recepts.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.recepts.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('recepts.create');
        $request->validate([
            'user_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'created_date' => 'required|date',
        ]);

        try {
            $row = new Recept();
            $row->user_id = $request->user_id;
            $row->branch_id = auth()->user()->branch_id;
            $row->created_date = $request->created_date;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, 'Recept created successfully.');
            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('recepts.edit');
        $data['pageHeader'] = $this->pageHeader;

        if ($data['edited'] = Recept::where('branch_id', auth()->user()->branch_id)->find($id)) {
            return view('backend.pages.recepts.edit', $data);
        } else {
            return RedirectHelper::routeError($this->index_route, 'Recept not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('recepts.edit');
        $request->validate([
            'user_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'created_date' => 'required|date',
        ]);

        try {
            if ($row = Recept::where('branch_id', auth()->user()->branch_id)->find($id)) {
                $row->user_id = $request->user_id;
                $row->branch_id = auth()->user()->branch_id;
                $row->created_date = $request->created_date;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Recept updated successfully.');
                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, 'Recept not found.');
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('recepts.delete');
        $deleteData = Recept::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}

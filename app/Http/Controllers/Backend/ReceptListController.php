<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\ReceptList;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ReceptListController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.receptlists.index";
    public $create_route = "admin.receptlists.create";
    public $store_route = "admin.receptlists.store";
    public $edit_route = "admin.receptlists.edit";
    public $update_route = "admin.receptlists.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Recept Lists",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/receptlists'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('receptlists.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = ReceptList::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.receptlists.index', $data);
    }

    // public function create()
    // {
    //     $this->checkOwnPermission('receptlists.create');
    //     $data['pageHeader'] = $this->pageHeader;
    //     return view('backend.pages.receptlists.create', $data);
    // }
    public function create(Request $request)
    {
        $this->checkOwnPermission('receptlists.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['recept_id'] = $request->get('recept_id'); 

        return view('backend.pages.receptlists.create', $data);
    }


    public function store(Request $request)
    {
        $this->checkOwnPermission('receptlists.create');
        $request->validate([
            'user_id' => 'required|integer',
            'recept_id' => 'required|integer',
            'service_id' => 'required|integer',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|numeric',
            'branch_id' => 'required|integer',
        ]);

        try {
            $row = new ReceptList();
            $row->user_id = $request->user_id;
            $row->recept_id = $request->recept_id;
            $row->service_id = $request->service_id;
            $row->price = $request->price;
            $row->discount = $request->discount ?? 0;
            $row->amount = $request->amount;
            $row->branch_id = auth()->user()->branch_id;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, 'Recept List created successfully.');
            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('receptlists.edit');
        $data['pageHeader'] = $this->pageHeader;

        if ($data['edited'] = ReceptList::where('branch_id', auth()->user()->branch_id)->find($id)) {
            return view('backend.pages.receptlists.edit', $data);
        } else {
            return RedirectHelper::routeError($this->index_route, 'Recept List not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('receptlists.edit');
        $request->validate([
            'user_id' => 'required|integer',
            'recept_id' => 'required|integer',
            'service_id' => 'required|integer',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|numeric',
            'branch_id' => 'required|integer',
        ]);

        try {
            if ($row = ReceptList::where('branch_id', auth()->user()->branch_id)->find($id)) {
                $row->user_id = $request->user_id;
                $row->recept_id = $request->recept_id;
                $row->service_id = $request->service_id;
                $row->price = $request->price;
                $row->discount = $request->discount ?? 0;
                $row->amount = $request->amount;
                $row->branch_id = auth()->user()->branch_id;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Recept List updated successfully.');
                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, 'Recept List not found.');
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('receptlists.delete');
        $deleteData = ReceptList::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}

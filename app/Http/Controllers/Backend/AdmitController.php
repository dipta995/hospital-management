<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Admit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class AdmitController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.admits.index";
    public $create_route = "admin.admits.create";
    public $store_route = "admin.admits.store";
    public $edit_route = "admin.admits.edit";
    public $update_route = "admin.admits.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "admits",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/admits'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('admits.index');

        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Admit::with('reefer')->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);

        $data['users'] = User::all();

        return view('backend.pages.admits.index', $data);
    }


    public function create()
    {
        $this->checkOwnPermission('admits.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['user'] = User::find(request('for'));

        if (!$data['user']) {
            return RedirectHelper::routeError($this->index_route, 'User not found.');
        }

        return view('backend.pages.admits.create', $data);
    }


    public function store(Request $request)
    {
        $this->checkOwnPermission('admits.create');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'admit_at' => 'required|string',
        ]);

        try {
            $row = new Admit();
            $row->user_id = $request->user_id;
            $row->branch_id = auth()->user()->branch_id;
            $row->reffer_id = $request->dr_refer_id;
            $row->admit_at = $request->admit_at ? Carbon::parse($request->admit_at)->format('Y-m-d H:i:s') : null;
            $row->release_at = $request->release_at ? Carbon::parse($request->release_at)->format('Y-m-d H:i:s') : null;
            $row->nid = $request->nid;
            $row->note = $request->note;
            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, 'Admit created successfully.');
            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }


    public function edit($id)
    {
        $this->checkOwnPermission('admits.edit');

        $data['pageHeader'] = $this->pageHeader;
        $data['edited'] = \App\Models\Admit::findOrFail($id);
        $data['users'] = \App\Models\User::all();

        return view('backend.pages.admits.edit', $data);
    }


    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('admits.edit');
        $request->validate([
            'name' => 'required|max:200',
            'price' => 'required|numeric',
        ]);

        try {
            if ($row = Admit::where('branch_id', auth()->user()->branch_id)->find($id)) {
//                $row->reffer_id = $request->dr_refer_id;
                $row->admit_at = $request->admit_at ? Carbon::parse($request->admit_at)->format('Y-m-d H:i:s') : null;
                $row->release_at = $request->release_at ? Carbon::parse($request->release_at)->format('Y-m-d H:i:s') : null;
                $row->nid = $request->nid;
                $row->note = $request->note;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Admit updated successfully.');
                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, 'Admit not found.');
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('admits.delete');
        $deleteData = Admit::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }

    public function storeRelease(Request $request, $id)
    {
        $admit = Admit::findOrFail($id);
        if (!$admit->release_at) {
            $admit->release_at = $request->release_at ? Carbon::parse($request->release_at)->format('Y-m-d H:i:s') : null;
            $admit->save();
            return response()->json(['status' => 200, 'message' => 'Release date added successfully']);
        }
        return response()->json(['status' => 400, 'message' => 'Release date already exists']);
    }


}

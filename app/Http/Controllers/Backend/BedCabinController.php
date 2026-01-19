<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\BedCabin;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class BedCabinController extends Controller
{
    public $pageHeader;
    public $index_route = 'admin.bed_cabins.index';
    public $create_route = 'admin.bed_cabins.create';
    public $store_route = 'admin.bed_cabins.store';
    public $edit_route = 'admin.bed_cabins.edit';
    public $update_route = 'admin.bed_cabins.update';

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => 'Bed / Cabin',
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/bed-cabins'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('bed_cabins.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = BedCabin::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('backend.pages.bed_cabins.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('bed_cabins.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.bed_cabins.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('bed_cabins.create');
        $request->validate([
            'name' => 'required|max:200',
            'type' => 'required|in:bed,cabin',
            'status' => 'required|in:available,occupied,maintenance',
            'price' => 'nullable|numeric',
        ]);

        try {
            $row = new BedCabin();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->type = $request->type;
            $row->status = $request->status ?? 'available';
            $row->price = $request->price;
            $row->note = $request->note;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, 'Bed/Cabin created successfully.');
            }

            return RedirectHelper::backWithInput();
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('bed_cabins.edit');
        $data['pageHeader'] = $this->pageHeader;

        if ($data['edited'] = BedCabin::where('branch_id', auth()->user()->branch_id)->find($id)) {
            return view('backend.pages.bed_cabins.edit', $data);
        }

        return RedirectHelper::routeError($this->index_route, 'Bed/Cabin not found.');
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('bed_cabins.edit');
        $request->validate([
            'name' => 'required|max:200',
            'type' => 'required|in:bed,cabin',
            'status' => 'required|in:available,occupied,maintenance',
            'price' => 'nullable|numeric',
        ]);

        try {
            if ($row = BedCabin::where('branch_id', auth()->user()->branch_id)->find($id)) {
                $row->name = $request->name;
                $row->type = $request->type;
                $row->status = $request->status;
                $row->price = $request->price;
                $row->note = $request->note;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Bed/Cabin updated successfully.');
                }

                return RedirectHelper::backWithInput();
            }

            return RedirectHelper::routeError($this->index_route, 'Bed/Cabin not found.');
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('bed_cabins.delete');
        $deleteData = BedCabin::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            }

            return response()->json(['status' => 422]);
        }
    }
}

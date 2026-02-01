<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Earn;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class EarnController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.earns.index";
    public $create_route = "admin.earns.create";
    public $store_route = "admin.earns.store";
    public $edit_route = "admin.earns.edit";
    public $update_route = "admin.earns.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Earning",
            'sub_title' => "",
            'plural_name' => "earns",
            'singular_name' => "Earn",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/earns'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check permission
        $this->checkOwnPermission('earns.index');

        // Initialize pageHeader
        $data['pageHeader'] = $this->pageHeader;

        // Start building the query
        $query = Earn::where('branch_id', auth()->user()->branch_id);

        // Apply month filter if requested
        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('date', $request->month); // Filter by month using the 'date' column
        }

        // Apply type filter if requested
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Fetch the filtered and paginated data
        $data['datas'] = $query->orderBy('id', 'DESC')->paginate(10);

        // Calculate the total earnings based on the filters
        $data['totalEarnings'] = $query->sum('amount');

        // Return the view with the data
        return view('backend.pages.earns.index', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('earns.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.earns.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//   dd(auth()->user()->branch_id);
        $this->checkOwnPermission('earns.create');
        $rules = [
            'name' => 'required|max:200',
            'type' => 'required|max:200',
            'amount' => 'required|max:200',
            'date' => 'required|max:200',
            'note' => 'required',
        ];
        $request->validate($rules);
//        try {
            $row = new Earn();
            $row->branch_id = auth()->user()->branch_id;
            $row->type = $request->type;
            $row->name = $request->name;
            $row->amount = $request->amount;
            $row->note = $request->note;
            $row->date = $request->date;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Earn Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
//        } catch (QueryException $e) {
//            return $e;
//            return RedirectHelper::backWithInputFromException();
//        }

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
    $this->checkOwnPermission('earns.edit');

    $data['pageHeader'] = $this->pageHeader;

    if ($data['data'] = Earn::where('branch_id', auth()->user()->branch_id)->find($id)) {
        return view('backend.pages.earns.edit', $data);
    } else {
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
        $this->checkOwnPermission('earns.edit');
        $request->validate([
            'name' => 'required|max:200',
            'type' => 'required|max:200',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        try {
            $row = Earn::where('branch_id', auth()->user()->branch_id)->find($id);
            if ($row) {
                $row->name = $request->name;
                $row->type = $request->type;
                $row->amount = $request->amount;
                $row->note = $request->note;
                $row->date = $request->date;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Earn updated successfully.');
                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, '<strong>Sorry!!!</strong> Data not found.');
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
{
    $this->checkOwnPermission('earns.delete');

    $deleteData = Earn::where('branch_id', auth()->user()->branch_id)->find($id);

    if ($deleteData) {
        if ($deleteData->delete()) {
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => 'Failed to delete the data.'
            ]);
        }
    } else {
        return response()->json([
            'status' => 404,
            'success' => false,
            'message' => 'Data not found.'
        ]);
    }
}

}

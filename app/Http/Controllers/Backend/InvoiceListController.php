<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\InvoiceList;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class InvoiceListController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.invoice_lists.index";
    public $create_route = "admin.invoice_lists.create";
    public $store_route = "admin.invoice_lists.store";
    public $edit_route = "admin.invoice_lists.edit";
    public $update_route = "admin.invoice_lists.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Invoices",
            'sub_title' => "",
            'plural_name' => "invoice_lists",
            'singular_name' => "InvoiceList",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/invoice_lists'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('invoice_lists.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.invoice_lists.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('invoice_lists.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.invoice_lists.create', $data);
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
        $this->checkOwnPermission('invoice_lists.create');
        $rules = [
            'name' => 'required|max:200',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|max:250',
        ];
        $request->validate($rules);
        try {
            $row = new InvoiceList();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->price = $request->price;
            $row->description = $request->description;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> InvoiceList Created Successfully');

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
        $this->checkOwnPermission('invoice_lists.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
        return view('backend.pages.invoice_lists.edit', $data);
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
        $this->checkOwnPermission('invoice_lists.edit');

            $request->validate([
                'name' => 'required|max:200',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|max:250',
            ]);
            try {
                if($row = InvoiceList::where('branch_id', auth()->user()->branch_id)
                    ->find($id)){
                $row->name = $request->name;
                $row->price = $request->price;
                $row->description = $request->description;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> InvoiceList Created Successfully');

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
        $this->checkOwnPermission('invoice_lists.delete');
        $deleteData = InvoiceList::where('branch_id', auth()->user()->branch_id)
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

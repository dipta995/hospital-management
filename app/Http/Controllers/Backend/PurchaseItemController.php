<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PurchaseItem;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PurchaseItemController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.purchase_items.index";
    public $create_route = "admin.purchase_items.create";
    public $store_route = "admin.purchase_items.store";
    public $edit_route = "admin.purchase_items.edit";
    public $update_route = "admin.purchase_items.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "PurchaseItems",
            'sub_title' => "",
            'plural_name' => "purchase_items",
            'singular_name' => "PurchaseItem",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/purchase_items'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('purchase_items.index');
        $data['pageHeader'] = $this->pageHeader;
        $searchTerm = \request('search');
        $data['datas'] = PurchaseItem::where('branch_id', auth()->user()->branch_id)
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('code', 'LIKE', '%' . $searchTerm . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('backend.pages.purchase_items.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('purchase_items.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = Category::where('branch_id', auth()->user()->branch_id)
            ->get(['id', 'name']);
        $branchId = auth()->user()->branch_id;

        // Get the latest product code for the same branch
        $latestPurchaseItem = PurchaseItem::where('branch_id', $branchId)->orderBy('code', 'DESC')->first();
        $data['nextCode'] = $latestPurchaseItem ? $latestPurchaseItem->code + 1 : 1001;
        return view('backend.pages.purchase_items.create', $data);
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
        $branchId = auth()->user()->branch_id;
        $this->checkOwnPermission('purchase_items.create');
        $rules = [
            'category_id' => 'required',
            'name' => 'required|max:200',
            'price' => 'required|numeric|min:0',
            'reefer_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable',
            'code' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($branchId) {
                    if (PurchaseItem::where('code', $value)->where('branch_id', $branchId)->exists()) {
                        $fail('The product code already exists for this branch.');
                    }
                },
            ],
        ];
        $request->validate($rules);
        try {

            $row = new PurchaseItem();
            $row->branch_id = auth()->user()->branch_id;
            $row->category_id = $request->category_id;
            $row->name = $request->name;
            $row->code = $request->code;
            $row->price = $request->price;
            $row->description = $request->description;
            $row->reefer_fee = $request->reefer_fee ?? 0;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> PurchaseItem Created Successfully');

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
        $this->checkOwnPermission('purchase_items.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = PurchaseItem::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            return view('backend.pages.purchase_items.edit', $data);
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
        $this->checkOwnPermission('purchase_items.edit');
        $branchId = auth()->user()->branch_id;
        $request->validate([
//                'category_id' => 'required',
            'name' => 'required|max:200',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable',
            'code' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($branchId, $id) {
                    if (PurchaseItem::where('code', $value)
                        ->where('branch_id', $branchId)
                        ->where('id', '!=', $id) // Ignore current product's ID
                        ->exists()) {
                        $fail('The product code already exists for this branch.');
                    }
                },
            ],
        ]);
        try {
//               return $request;
            if ($row = PurchaseItem::where('branch_id', auth()->user()->branch_id)
                ->find($id)) {

//                    $row->category_id = $request->category_id;
                $row->name = $request->name;
                $row->code = $request->code;
                $row->price = $request->price;
                $row->description = $request->description;
                $row->reefer_fee = $request->reefer_fee ?? 0;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> PurchaseItem Created Successfully');

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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        $this->checkOwnPermission('purchase_items.delete');
        $deleteData = PurchaseItem::where('branch_id', auth()->user()->branch_id)
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

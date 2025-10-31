<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Setting;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PurchaseController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.purchases.index";
    public $create_route = "admin.purchases.create";
    public $store_route = "admin.purchases.store";
    public $edit_route = "admin.purchases.edit";
    public $update_route = "admin.purchases.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Purchase",
            'sub_title' => "",
            'plural_name' => "purchases",
            'singular_name' => "Purchase",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/purchases'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('purchases.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Purchase::withSum('purchasePaid', 'amount')
            ->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('backend.pages.purchases.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('purchases.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['items'] = Item::where('branch_id', auth()->user()->branch_id)->get();
        $data['suppliers'] = Supplier::where('branch_id', auth()->user()->branch_id)->get();
        return view('backend.pages.purchases.create', $data);
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
        $this->checkOwnPermission('purchases.create');
        $rules = [
            'supplier_id' => 'required',
            'purchase_date' => 'required|date',
            'total_cost' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',

        ];
        $request->validate($rules);
        try {
            DB::beginTransaction();
            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'supplier_id' => $request['supplier_id'],
                'purchase_date' => $request['purchase_date'],
                'total_cost' => $request['total_cost'],
                'due_amount' => $request['due_amount'],
            ]);

            $payment = Payment::create([
                'branch_id' => $branchId,
                'purchase_id' => $purchase->id,
                'amount' => $request['paid_amount'],
                'payment_date' => $request->date ?? Carbon::now('Asia/Dhaka')->format('Y-m-d'),
                'payment_method' => $request['payment_method'],
            ]);

            foreach ($request['items'] as $item) {
                PurchaseItem::create([
                    'branch_id' => $branchId,
                    'item_id' => $item['item_id'],
                    'supplier_id' => $request['supplier_id'],
                    'purchase_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'],
                    'total_amount' => ($item['unit_price'] * $item['quantity']),
                    'expiry_date' => $item['expiry_date'],
                ]);
            }
            $cost = new Cost();
            $cost->branch_id = auth()->user()->branch_id;
            $cost->cost_category_id = Setting::get('purchase_category');
            $cost->reason = 'Purchase';
            $cost->amount = $request['paid_amount'];
            $cost->invoice_id = null;
            $cost->payment_id = $payment->id;
            $cost->refer_id = null;
            $cost->account_details = null;
            $cost->account_type = null;
            $cost->payment_type = $request['payment_method'];
            $cost->creation_date = $request->date ?? Carbon::now('Asia/Dhaka')->format('Y-m-d');
            $cost->save();

            DB::commit();

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Purchase Created Successfully');
        } catch (QueryException $e) {
            DB::rollBack();
//            dd($e);
            return RedirectHelper::backWithInputFromException($e);
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
        $this->checkOwnPermission('purchases.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['purchase'] = Purchase::withSum('purchasePaid', 'amount')->with('supplier', 'purchaseItems', 'item')->findOrFail($id);

        return view('backend.pages.purchases.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkOwnPermission('purchases.edit');
        $data['pageHeader'] = $this->pageHeader;

        // Retrieve the purchase and associated items
        $data['purchase'] = Purchase::with('purchaseItems', 'supplier')
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        // Get all available items and suppliers for the form
        $data['items'] = Item::where('branch_id', auth()->user()->branch_id)->get();
        $data['suppliers'] = Supplier::where('branch_id', auth()->user()->branch_id)->get();

        return view('backend.pages.purchases.edit', $data);
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
        $this->checkOwnPermission('purchases.edit');
        $rules = [
            'supplier_id' => 'required',
            'purchase_date' => 'required|date',
            'total_cost' => 'required|numeric|min:0',
//            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
        ];
        $request->validate($rules);

        try {
            // Retrieve the existing purchase
            $purchase = Purchase::findOrFail($id);

            // Update the purchase details
            $purchase->update([
                'supplier_id' => $request['supplier_id'],
                'purchase_date' => $request['purchase_date'],
                'total_cost' => $request['total_cost'],
//                'paid_amount' => $request['paid_amount'],
                'due_amount' => $request['due_amount'],
            ]);

            // Update the items associated with this purchase
            foreach ($request['items'] as $item) {
                PurchaseItem::updateOrCreate(
                    ['purchase_id' => $purchase->id, 'item_id' => $item['item_id']],
                    [
                        'branch_id' => auth()->user()->branch_id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount_amount' => $item['discount_amount'],
                        'total_amount' => ($item['unit_price'] * $item['quantity']),
                        'expiry_date' => $item['expiry_date'],
                    ]
                );
            }

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Success!</strong> Purchase Updated Successfully');
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function purchasePayment(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $row = new Payment();
        $row->branch_id = $branchId;
        $row->purchase_id = $request->purchase_id;
        $row->amount = $request->amount;
        $row->payment_method = $request->payment_type;
        $row->payment_date = $request->date ?? Carbon::now('Asia/Dhaka')->format('Y-m-d');
        if($row->save()){
            $cost = new Cost();
            $cost->branch_id = $branchId;
            $cost->cost_category_id = Setting::get('purchase_category');
            $cost->reason = 'Purchase (' . ($request->account_no ?? " ") . ')-' . $request->supplier_name;

            $cost->amount = $request->amount;
            $cost->invoice_id = null;
            $cost->payment_id = $row->id;
            $cost->refer_id = null;
            $cost->account_details = null;
            $cost->account_type = null;
            $cost->payment_type = $request->payment_type;
            $cost->creation_date = $request->date ?? Carbon::now('Asia/Dhaka')->format('Y-m-d');
            if($cost->save()) {

                return RedirectHelper::routeSuccessWithSubParam('admin.purchases.show',$request->purchase_id, '<strong>Success!</strong> Purchase Store Successfully');

            }
        }else{
            return RedirectHelper::backWithInputFromException();

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
        $this->checkOwnPermission('purchases.delete');
        try {
            // Retrieve the purchase
            $purchase = Purchase::findOrFail($id);

            // Delete associated items first
            $purchase->purchaseItems()->delete();

            // Delete the purchase record
            $purchase->delete();

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Success!</strong> Purchase Deleted Successfully');
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function items()
    {
        $this->checkOwnPermission('purchases.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = PurchaseItem::where('branch_id', auth()->user()->branch_id)
            ->latest()->paginate(20);
        return view('backend.pages.purchases.items', $data);
    }

    public function editItem($id)
    {
        $this->checkOwnPermission('purchases.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['edited'] = PurchaseItem::with('item', 'purchase')->findOrFail($id);


        return view('backend.pages.purchases.edit-item', $data);
    }

    public function updateItem(Request $request, $id)
    {
        if ($row = PurchaseItem::find($id)) {
            $row->quantity = $request->quantity;
            $row->quantity_spend = $request->quantity_spend;
            $row->save();
            return RedirectHelper::routeSuccess('admin.items.purchases', '<strong>Success!</strong> Purchase Deleted Successfully');

        } else {
            return RedirectHelper::backWithInputFromException();

        }


        return view('backend.pages.purchases.edit-item', $data);
    }


}

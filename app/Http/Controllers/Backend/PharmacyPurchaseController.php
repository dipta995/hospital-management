<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\PharmacyProduct;
use App\Models\PharmacyPurchase;
use App\Models\PharmacyPurchaseItem;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PharmacyPurchaseController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.pharmacy_purchases.index";
    public $create_route = "admin.pharmacy_purchases.create";
    public $store_route = "admin.pharmacy_purchases.store";
    public $edit_route = "admin.pharmacy_purchases.edit";
    public $update_route = "admin.pharmacy_purchases.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Pharmacy Purchases",
            'sub_title' => "",
            'plural_name' => "pharmacy_purchases",
            'singular_name' => "PharmacyPurchase",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/pharmacy-purchases'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('pharmacy_purchases.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = PharmacyPurchase::with('supplier')
            ->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('backend.pages.pharmacy_purchases.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('pharmacy_purchases.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['products'] = PharmacyProduct::where('status', true)->orderBy('name')->get();
        $data['suppliers'] = Supplier::where('branch_id', auth()->user()->branch_id)->get();

        return view('backend.pages.pharmacy_purchases.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('pharmacy_purchases.create');
        $branchId = auth()->user()->branch_id;

        $rules = [
            'supplier_id' => 'required|integer',
            'purchase_date' => 'required|date',
            'total_cost' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
        ];
        $request->validate($rules);

        try {
            \DB::beginTransaction();

            $purchase = PharmacyPurchase::create([
                'branch_id' => $branchId,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_cost' => $request->total_cost,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'status' => $request->due_amount <= 0 ? 'Paid' : 'Partially Paid',
            ]);

            foreach ($request->items as $item) {
                if (empty($item['pharmacy_product_id'])) {
                    continue;
                }

                PharmacyPurchaseItem::create([
                    'branch_id' => $branchId,
                    'pharmacy_product_id' => $item['pharmacy_product_id'],
                    'supplier_id' => $request->supplier_id,
                    'pharmacy_purchase_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_amount' => ($item['unit_price'] * $item['quantity']) - ($item['discount_amount'] ?? 0),
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);
            }

            \DB::commit();

            return response()->json(['message' => 'Pharmacy purchase created successfully']);
        } catch (QueryException $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('pharmacy_purchases.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['purchase'] = PharmacyPurchase::with('items')->where('branch_id', auth()->user()->branch_id)->findOrFail($id);
        $data['products'] = PharmacyProduct::where('status', true)->orderBy('name')->get();
        $data['suppliers'] = Supplier::where('branch_id', auth()->user()->branch_id)->get();

        return view('backend.pages.pharmacy_purchases.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('pharmacy_purchases.edit');
        $branchId = auth()->user()->branch_id;

        $rules = [
            'supplier_id' => 'required|integer',
            'purchase_date' => 'required|date',
            'total_cost' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
        ];
        $request->validate($rules);

        try {
            \DB::beginTransaction();

            $purchase = PharmacyPurchase::where('branch_id', $branchId)->findOrFail($id);
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_cost' => $request->total_cost,
                'due_amount' => $request->due_amount,
                'status' => $request->due_amount <= 0 ? 'Paid' : 'Partially Paid',
            ]);

            // Simple approach: delete old items and recreate
            PharmacyPurchaseItem::where('pharmacy_purchase_id', $purchase->id)->delete();

            foreach ($request->items as $item) {
                if (empty($item['pharmacy_product_id'])) {
                    continue;
                }

                PharmacyPurchaseItem::create([
                    'branch_id' => $branchId,
                    'pharmacy_product_id' => $item['pharmacy_product_id'],
                    'supplier_id' => $request->supplier_id,
                    'pharmacy_purchase_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_amount' => ($item['unit_price'] * $item['quantity']) - ($item['discount_amount'] ?? 0),
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);
            }

            \DB::commit();

            return response()->json(['message' => 'Pharmacy purchase updated successfully']);
        } catch (QueryException $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('pharmacy_purchases.delete');

        $purchase = PharmacyPurchase::where('branch_id', auth()->user()->branch_id)->find($id);
        if (!$purchase) {
            return response()->json(['status' => 404]);
        }

        PharmacyPurchaseItem::where('pharmacy_purchase_id', $purchase->id)->delete();
        if ($purchase->delete()) {
            return response()->json(['status' => 200]);
        }

        return response()->json(['status' => 422]);
    }
}

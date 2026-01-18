<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\PharmacyProduct;
use App\Models\PharmacySale;
use App\Models\PharmacySaleItem;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PharmacySaleController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.pharmacy_sales.index";
    public $create_route = "admin.pharmacy_sales.create";
    public $store_route = "admin.pharmacy_sales.store";
    public $edit_route = "admin.pharmacy_sales.edit";
    public $update_route = "admin.pharmacy_sales.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Pharmacy Sales",
            'sub_title' => "",
            'plural_name' => "pharmacy_sales",
            'singular_name' => "PharmacySale",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/pharmacy-sales'),
        ];
    }

    public function index()
    {
        $this->checkOwnPermission('pharmacy_sales.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = PharmacySale::with(['customer','doctor'])
            ->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return view('backend.pages.pharmacy_sales.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('pharmacy_sales.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['products'] = PharmacyProduct::where('status', true)->orderBy('name')->get();

        return view('backend.pages.pharmacy_sales.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('pharmacy_sales.create');
        $branchId = auth()->user()->branch_id;

        $rules = [
            'customer_id' => 'required|integer',
            'dr_refer_id' => 'nullable|integer',
            'sale_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
        ];
        $request->validate($rules);

        try {
            \DB::beginTransaction();

            $sale = PharmacySale::create([
                'branch_id' => $branchId,
                'customer_id' => $request->customer_id,
                'dr_refer_id' => $request->dr_refer_id,
                'sale_date' => $request->sale_date,
                'total_amount' => $request->total_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
            ]);

            foreach ($request->items as $item) {
                if (empty($item['pharmacy_product_id'])) {
                    continue;
                }

                PharmacySaleItem::create([
                    'branch_id' => $branchId,
                    'pharmacy_sale_id' => $sale->id,
                    'pharmacy_product_id' => $item['pharmacy_product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_amount' => $item['total_amount'],
                ]);
            }

            \DB::commit();

            return response()->json([
                'message' => 'Pharmacy sale created successfully',
                'sale_id' => $sale->id,
                'customer_name' => optional($sale->customer)->name,
            ]);
        } catch (QueryException $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('pharmacy_sales.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['sale'] = PharmacySale::with('items')->where('branch_id', auth()->user()->branch_id)->findOrFail($id);
        $data['customer'] = User::find($data['sale']->customer_id);
        $data['products'] = PharmacyProduct::where('status', true)->orderBy('name')->get();

        return view('backend.pages.pharmacy_sales.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('pharmacy_sales.edit');
        $branchId = auth()->user()->branch_id;

        $rules = [
            'customer_id' => 'required|integer',
            'dr_refer_id' => 'nullable|integer',
            'sale_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
        ];
        $request->validate($rules);

        try {
            \DB::beginTransaction();

            $sale = PharmacySale::where('branch_id', $branchId)->findOrFail($id);
            $sale->update([
                'customer_id' => $request->customer_id,
                'dr_refer_id' => $request->dr_refer_id,
                'sale_date' => $request->sale_date,
                'total_amount' => $request->total_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
            ]);

            PharmacySaleItem::where('pharmacy_sale_id', $sale->id)->delete();

            foreach ($request->items as $item) {
                if (empty($item['pharmacy_product_id'])) {
                    continue;
                }

                PharmacySaleItem::create([
                    'branch_id' => $branchId,
                    'pharmacy_sale_id' => $sale->id,
                    'pharmacy_product_id' => $item['pharmacy_product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'total_amount' => $item['total_amount'],
                ]);
            }

            \DB::commit();

            return response()->json(['message' => 'Pharmacy sale updated successfully']);
        } catch (QueryException $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('pharmacy_sales.delete');

        $sale = PharmacySale::where('branch_id', auth()->user()->branch_id)->find($id);
        if (!$sale) {
            return response()->json(['status' => 404]);
        }

        PharmacySaleItem::where('pharmacy_sale_id', $sale->id)->delete();
        if ($sale->delete()) {
            return response()->json(['status' => 200]);
        }

        return response()->json(['status' => 422]);
    }

    public function pdfPreview($id)
    {
        $this->checkOwnPermission('pharmacy_sales.index');

        $sale = PharmacySale::with(['customer', 'doctor', 'items.product'])
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        return view('backend.pages.pharmacy_sales.invoice-regular', compact('sale'));
    }
}

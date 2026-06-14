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
        $branchId = auth()->user()->branch_id;
        $data['pageHeader'] = $this->pageHeader;
        $data['items'] = Item::where('branch_id', $branchId)->orderBy('name')->get();
        $data['suppliers'] = Supplier::where('branch_id', $branchId)->orderBy('name')->get();

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
        $branchId = auth()->user()->branch_id;
        $this->checkOwnPermission('purchases.create');

        $items = $this->normalizePurchaseItems($request->input('items', []));

        $request->validate([
            'supplier_id' => 'required|integer',
            'purchase_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        if (empty($items)) {
            return $this->purchaseJsonError($request, 'Add at least one item with quantity and price.');
        }

        $totalCost = round(collect($items)->sum('total_amount'), 2);
        $paidAmount = round((float) $request->paid_amount, 2);
        $dueAmount = max($totalCost - $paidAmount, 0);

        try {
            DB::beginTransaction();

            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_cost' => $totalCost,
                'due_amount' => $dueAmount,
            ]);

            $payment = null;
            if ($paidAmount > 0) {
                $payment = Payment::create([
                    'branch_id' => $branchId,
                    'purchase_id' => $purchase->id,
                    'amount' => $paidAmount,
                    'payment_date' => $request->purchase_date,
                    'payment_method' => $request->payment_method,
                ]);
            }

            foreach ($items as $item) {
                PurchaseItem::create([
                    'branch_id' => $branchId,
                    'item_id' => $item['item_id'],
                    'supplier_id' => $request->supplier_id,
                    'purchase_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'],
                    'total_amount' => $item['total_amount'],
                    'expiry_date' => $item['expiry_date'],
                ]);
            }

            if ($payment && $paidAmount > 0) {
                Cost::create([
                    'branch_id' => $branchId,
                    'cost_category_id' => Setting::get('purchase_category'),
                    'reason' => 'Purchase',
                    'amount' => $paidAmount,
                    'invoice_id' => null,
                    'payment_id' => $payment->id,
                    'refer_id' => null,
                    'account_details' => null,
                    'account_type' => null,
                    'payment_type' => $request->payment_method,
                    'creation_date' => $request->purchase_date,
                ]);
            }

            DB::commit();

            return $this->purchaseJsonSuccess($request, 'Purchase created successfully.');
        } catch (QueryException $e) {
            DB::rollBack();

            return $this->purchaseJsonError($request, 'Something went wrong while saving the purchase.');
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

        $data['purchase'] = Purchase::with(['purchaseItems.item', 'supplier'])
            ->withSum('purchasePaid', 'amount')
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        $data['purchaseItems'] = $data['purchase']->purchaseItems;
        $data['items'] = Item::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();
        $data['suppliers'] = Supplier::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();

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
        $branchId = auth()->user()->branch_id;
        $items = $this->normalizePurchaseItems($request->input('items', []));

        $request->validate([
            'supplier_id' => 'required|integer',
            'purchase_date' => 'required|date',
        ]);

        if (empty($items)) {
            return $this->purchaseJsonError($request, 'Add at least one item with quantity and price.');
        }

        try {
            $purchase = Purchase::where('branch_id', $branchId)->findOrFail($id);
            $totalCost = round(collect($items)->sum('total_amount'), 2);
            $paidAmount = (float) ($purchase->purchase_paid_sum_amount ?? Payment::where('purchase_id', $purchase->id)->sum('amount'));
            $dueAmount = max($totalCost - $paidAmount, 0);

            DB::beginTransaction();

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_cost' => $totalCost,
                'due_amount' => $dueAmount,
            ]);

            PurchaseItem::where('purchase_id', $purchase->id)->delete();

            foreach ($items as $item) {
                PurchaseItem::create([
                    'branch_id' => $branchId,
                    'item_id' => $item['item_id'],
                    'supplier_id' => $request->supplier_id,
                    'purchase_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'],
                    'total_amount' => $item['total_amount'],
                    'expiry_date' => $item['expiry_date'],
                ]);
            }

            DB::commit();

            return $this->purchaseJsonSuccess($request, 'Purchase updated successfully.');
        } catch (QueryException $e) {
            DB::rollBack();

            return $this->purchaseJsonError($request, 'Something went wrong while updating the purchase.');
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
    public function items(Request $request)
    {
        $this->checkOwnPermission('purchases.index');
        $branchId = auth()->user()->branch_id;
        $search = $request->get('search');
        $expiryFilter = $request->get('expiry', 'all');
        $stockFilter = $request->get('stock', 'in_stock');
        $today = Carbon::today();

        $query = PurchaseItem::with(['item', 'supplier', 'purchase'])
            ->where('branch_id', $branchId);

        if ($stockFilter === 'in_stock') {
            $query->whereRaw('(quantity - COALESCE(quantity_spend, 0)) > 0');
        } elseif ($stockFilter === 'depleted') {
            $query->whereRaw('(quantity - COALESCE(quantity_spend, 0)) <= 0');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', function ($itemQ) use ($search) {
                    $itemQ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('supplier', function ($supQ) use ($search) {
                    $supQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($expiryFilter === 'expired') {
            $query->whereNotNull('expiry_date')->whereDate('expiry_date', '<', $today);
        } elseif ($expiryFilter === 'unexpired') {
            $query->where(function ($q) use ($today) {
                $q->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', $today);
            });
        } elseif ($expiryFilter === 'soon') {
            $query->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', $today)
                ->whereDate('expiry_date', '<=', $today->copy()->addDays(7));
        } elseif ($expiryFilter === 'low') {
            $query->whereRaw('quantity > 0 AND (quantity_spend / quantity) >= 0.9');
        }

        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = $query
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expiry_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($row) {
                $row->remaining = max((int) $row->quantity - (int) $row->quantity_spend, 0);
                $row->used_pct = $row->quantity > 0
                    ? (int) round(((int) $row->quantity_spend / (int) $row->quantity) * 100)
                    : 0;
                return $row;
            });

        $base = PurchaseItem::where('branch_id', $branchId);
        $inStockSql = '(quantity - COALESCE(quantity_spend, 0)) > 0';

        $data['stats'] = [
            'total_lines' => (clone $base)->count(),
            'in_stock' => (clone $base)->whereRaw($inStockSql)->count(),
            'expired' => (clone $base)->whereRaw($inStockSql)->whereNotNull('expiry_date')->whereDate('expiry_date', '<', $today)->count(),
            'expiring_soon' => (clone $base)->whereRaw($inStockSql)->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', $today)
                ->whereDate('expiry_date', '<=', $today->copy()->addDays(7))
                ->count(),
        ];

        return view('backend.pages.purchases.items', $data);
    }

    public function editItem($id)
    {
        $this->checkOwnPermission('purchases.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['edited'] = PurchaseItem::with(['item', 'supplier', 'purchase'])
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        return view('backend.pages.purchases.edit-item', $data);
    }

    public function updateItem(Request $request, $id)
    {
        $this->checkOwnPermission('purchases.edit');

        $request->validate([
            'quantity' => 'required|integer|min:0',
            'quantity_spend' => 'required|integer|min:0',
        ]);

        $row = PurchaseItem::where('branch_id', auth()->user()->branch_id)->findOrFail($id);

        if ($request->quantity_spend > $request->quantity) {
            return RedirectHelper::backWithInputFromException();
        }

        $row->quantity = $request->quantity;
        $row->quantity_spend = $request->quantity_spend;

        if ($row->save()) {
            return RedirectHelper::routeSuccess('admin.items.purchases', '<strong>Success!</strong> Stock item updated successfully.');
        }

        return RedirectHelper::backWithInputFromException();
    }

    private function normalizePurchaseItems(array $rawItems): array
    {
        return collect($rawItems)
            ->filter(fn ($item) => !empty($item['item_id']))
            ->map(function ($item) {
                $qty = (float) ($item['quantity'] ?? 0);
                $price = (float) ($item['unit_price'] ?? 0);
                $discount = (float) ($item['discount_amount'] ?? 0);

                return [
                    'item_id' => (int) $item['item_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount_amount' => $discount,
                    'total_amount' => max(($qty * $price) - $discount, 0),
                    'expiry_date' => !empty($item['expiry_date']) ? $item['expiry_date'] : null,
                ];
            })
            ->values()
            ->all();
    }

    private function purchaseJsonSuccess(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route($this->index_route),
            ]);
        }

        return RedirectHelper::routeSuccess($this->index_route, '<strong>Success!</strong> ' . $message);
    }

    private function purchaseJsonError(Request $request, string $message, int $status = 422)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return RedirectHelper::backWithInputFromException();
    }
}

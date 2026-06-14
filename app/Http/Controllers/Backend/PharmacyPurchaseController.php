<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PharmacyProduct;
use App\Models\PharmacyPurchase;
use App\Models\PharmacyPurchaseItem;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

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
        $branchId = auth()->user()->branch_id;

        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = PharmacyPurchase::with(['supplier', 'items'])
            ->withCount('items')
            ->where('branch_id', $branchId)
            ->orderBy('id', 'DESC')
            ->paginate(15);

        $baseQuery = PharmacyPurchase::where('branch_id', $branchId);
        $data['stats'] = [
            'total_purchases' => (clone $baseQuery)->count(),
            'total_cost' => (clone $baseQuery)->sum('total_cost'),
            'total_due' => (clone $baseQuery)->sum('due_amount'),
            'this_month' => (clone $baseQuery)
                ->whereMonth('purchase_date', now()->month)
                ->whereYear('purchase_date', now()->year)
                ->count(),
        ];

        return view('backend.pages.pharmacy_purchases.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('pharmacy_purchases.create');
        $branchId = auth()->user()->branch_id;
        $stockMap = PharmacyProduct::stockMapForBranch($branchId);

        $data['pageHeader'] = $this->pageHeader;
        $data['products'] = PharmacyProduct::orderBy('name')->get()->map(function ($product) use ($stockMap) {
            $product->current_stock = $stockMap[$product->id] ?? 0;
            return $product;
        });
        $data['suppliers'] = Supplier::where('branch_id', $branchId)->orderBy('name')->get();
        $data['purchase'] = null;
        $data['purchaseItems'] = collect();

        return view('backend.pages.pharmacy_purchases.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('pharmacy_purchases.create');

        try {
            $branchId = auth()->user()->branch_id;
            $items = $this->normalizeItems($request->input('items', []));
            $this->validatePurchasePayload($request, $items, true);

            $totalCost = $this->sumLineTotals($items);
            $paidAmount = round((float) $request->paid_amount, 2);
            $dueAmount = max($totalCost - $paidAmount, 0);

            DB::beginTransaction();

            $purchase = PharmacyPurchase::create([
                'branch_id' => $branchId,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_cost' => $totalCost,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => $dueAmount <= 0 ? 'Paid' : 'Partially Paid',
            ]);

            $this->persistItems($purchase, $items, $branchId, (int) $request->supplier_id);

            DB::commit();

            return response()->json(['message' => 'Pharmacy purchase created successfully']);
        } catch (ValidationException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong while saving the purchase.'], 500);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('pharmacy_purchases.edit');
        $branchId = auth()->user()->branch_id;
        $stockMap = PharmacyProduct::stockMapForBranch($branchId);

        $data['pageHeader'] = $this->pageHeader;
        $data['purchase'] = PharmacyPurchase::with(['items.product'])
            ->where('branch_id', $branchId)
            ->findOrFail($id);
        $data['purchaseItems'] = $data['purchase']->items;
        $data['products'] = PharmacyProduct::orderBy('name')->get()->map(function ($product) use ($stockMap) {
            $product->current_stock = $stockMap[$product->id] ?? 0;
            return $product;
        });
        $data['suppliers'] = Supplier::where('branch_id', $branchId)->orderBy('name')->get();

        return view('backend.pages.pharmacy_purchases.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('pharmacy_purchases.edit');

        try {
            $branchId = auth()->user()->branch_id;
            $purchase = PharmacyPurchase::where('branch_id', $branchId)->findOrFail($id);
            $items = $this->normalizeItems($request->input('items', []));
            $this->validatePurchasePayload($request, $items, false);

            $qtyByProduct = [];
            foreach ($items as $item) {
                $pid = $item['pharmacy_product_id'];
                $qtyByProduct[$pid] = ($qtyByProduct[$pid] ?? 0) + $item['quantity'];
            }
            PharmacyProduct::assertPurchaseChangeSafe($branchId, $purchase->id, $qtyByProduct);

            $totalCost = $this->sumLineTotals($items);
            $paidAmount = round((float) $request->paid_amount, 2);
            $dueAmount = max($totalCost - $paidAmount, 0);

            DB::beginTransaction();

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_cost' => $totalCost,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => $dueAmount <= 0 ? 'Paid' : 'Partially Paid',
            ]);

            PharmacyPurchaseItem::where('pharmacy_purchase_id', $purchase->id)->delete();
            $this->persistItems($purchase, $items, $branchId, (int) $request->supplier_id);

            DB::commit();

            return response()->json(['message' => 'Pharmacy purchase updated successfully']);
        } catch (ValidationException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong while updating the purchase.'], 500);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('pharmacy_purchases.delete');

        try {
            $branchId = auth()->user()->branch_id;
            $purchase = PharmacyPurchase::where('branch_id', $branchId)->find($id);

            if (!$purchase) {
                return response()->json(['status' => 404, 'message' => 'Purchase not found.']);
            }

            PharmacyProduct::assertPurchaseDeleteSafe($branchId, $purchase->id);

            DB::beginTransaction();
            PharmacyPurchaseItem::where('pharmacy_purchase_id', $purchase->id)->delete();
            $purchase->delete();
            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Purchase deleted successfully.']);
        } catch (InvalidArgumentException $e) {
            return response()->json(['status' => 422, 'message' => $e->getMessage()]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['status' => 422, 'message' => 'Could not delete purchase.']);
        }
    }

    private function normalizeItems(array $rawItems): array
    {
        return collect($rawItems)
            ->filter(fn ($item) => !empty($item['pharmacy_product_id']))
            ->map(function ($item) {
                $qty = (float) ($item['quantity'] ?? 0);
                $price = (float) ($item['unit_price'] ?? 0);
                $discount = (float) ($item['discount_amount'] ?? 0);

                return [
                    'pharmacy_product_id' => (int) $item['pharmacy_product_id'],
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

    private function validatePurchasePayload(Request $request, array $items, bool $isCreate): void
    {
        $request->validate([
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'paid_amount' => ($isCreate ? 'required' : 'nullable') . '|numeric|min:0',
        ]);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => ['Add at least one product with quantity and price.'],
            ]);
        }

        foreach ($items as $index => $item) {
            if ($item['quantity'] < 1) {
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => ['Quantity must be at least 1.'],
                ]);
            }
            if ($item['unit_price'] < 0) {
                throw ValidationException::withMessages([
                    "items.{$index}.unit_price" => ['Unit price cannot be negative.'],
                ]);
            }
        }
    }

    private function sumLineTotals(array $items): float
    {
        return round(collect($items)->sum('total_amount'), 2);
    }

    private function persistItems(PharmacyPurchase $purchase, array $items, int $branchId, int $supplierId): void
    {
        foreach ($items as $item) {
            PharmacyPurchaseItem::create([
                'branch_id' => $branchId,
                'pharmacy_product_id' => $item['pharmacy_product_id'],
                'supplier_id' => $supplierId,
                'pharmacy_purchase_id' => $purchase->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_amount' => $item['discount_amount'],
                'total_amount' => $item['total_amount'],
                'expiry_date' => $item['expiry_date'],
            ]);
        }
    }
}

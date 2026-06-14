<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\PharmacyBrand;
use App\Models\PharmacyCategory;
use App\Models\PharmacyProduct;
use App\Models\PharmacyPurchaseItem;
use App\Models\PharmacySaleItem;
use App\Models\PharmacyType;
use App\Models\PharmacyUnit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rule;

class PharmacyProductController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.pharmacy_products.index";
    public $create_route = "admin.pharmacy_products.create";
    public $store_route = "admin.pharmacy_products.store";
    public $edit_route = "admin.pharmacy_products.edit";
    public $update_route = "admin.pharmacy_products.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Pharmacy Products",
            'sub_title' => "",
            'plural_name' => "pharmacy_products",
            'singular_name' => "PharmacyProduct",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/pharmacy-products'),

        ];
    }

    public function index()
    {
        $this->checkOwnPermission('pharmacy_products.index');
        $data['pageHeader'] = $this->pageHeader;
        $branchId = auth()->user()->branch_id;
        $search = request('search');
        $stockFilter = request('stock');

        $purchasedMap = PharmacyProduct::purchasedQuantitiesByBranch($branchId);
        $soldMap = PharmacyProduct::soldQuantitiesByBranch($branchId);

        $attachStock = function ($product) use ($purchasedMap, $soldMap) {
            $purchased = (float) ($purchasedMap[$product->id] ?? 0);
            $sold = (float) ($soldMap[$product->id] ?? 0);
            $product->stock_qty = max($purchased - $sold, 0);
            $product->purchased_qty = $purchased;
            $product->sold_qty = $sold;
            return $product;
        };

        $query = PharmacyProduct::with(['category', 'brand', 'type', 'quantityType'])
            ->orderBy('name');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if (in_array($stockFilter, ['low', 'out'], true)) {
            $filtered = $query->get()->map($attachStock);

            if ($stockFilter === 'low') {
                $filtered = $filtered->filter(fn ($p) => $p->stock_qty > 0 && $p->stock_qty <= $p->alert_qty);
            } else {
                $filtered = $filtered->filter(fn ($p) => $p->stock_qty <= 0);
            }

            $page = (int) request('page', 1);
            $perPage = 20;
            $data['datas'] = new \Illuminate\Pagination\LengthAwarePaginator(
                $filtered->forPage($page, $perPage)->values(),
                $filtered->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $data['datas'] = $query->paginate(20)->through($attachStock);
        }

        $totalPurchased = (float) $purchasedMap->sum();
        $totalSold = (float) $soldMap->sum();
        $allProducts = PharmacyProduct::get(['id', 'alert_qty']);
        $stockMap = PharmacyProduct::stockMapForBranch($branchId);

        $lowStock = 0;
        $outOfStock = 0;
        foreach ($allProducts as $product) {
            $stock = $stockMap[$product->id] ?? 0;
            if ($stock <= 0) {
                $outOfStock++;
            } elseif ($stock <= $product->alert_qty) {
                $lowStock++;
            }
        }

        $data['stats'] = [
            'total_products' => $allProducts->count(),
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total_stock_units' => max($totalPurchased - $totalSold, 0),
        ];

        return view('backend.pages.pharmacy_products.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('pharmacy_products.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = PharmacyCategory::orderBy('name')->get(['id', 'name']);
        $data['brands'] = PharmacyBrand::orderBy('name')->get(['id', 'name']);
        $data['types'] = PharmacyType::orderBy('name')->get(['id', 'name']);
        $data['units'] = PharmacyUnit::orderBy('name')->get(['id', 'name']);
        return view('backend.pages.pharmacy_products.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('pharmacy_products.create');

        $rules = [
            'category_id' => ['required', 'integer'],
            'brand_id' => ['required', 'integer'],
            'type_id' => ['required', 'integer'],
            'quantity_type_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'strength' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:pharmacy_products,barcode'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'alert_qty' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ];

        $request->validate($rules);

        try {
            $row = new PharmacyProduct();
            $row->category_id = $request->category_id;
            $row->brand_id = $request->brand_id;
            $row->type_id = $request->type_id;
            $row->quantity_type_id = $request->quantity_type_id;
            $row->name = $request->name;
            $row->generic_name = $request->generic_name;
            $row->strength = $request->strength;
            $row->barcode = $request->barcode;
            $row->purchase_price = $request->purchase_price;
            $row->sell_price = $request->sell_price;
            $row->alert_qty = $request->alert_qty;
            if (\Illuminate\Support\Facades\Schema::hasColumn('pharmacy_products', 'status')) {
                $row->status = $request->boolean('status', true) ? 1 : 0;
            }

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Product Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('pharmacy_products.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = PharmacyCategory::orderBy('name')->get(['id', 'name']);
        $data['brands'] = PharmacyBrand::orderBy('name')->get(['id', 'name']);
        $data['types'] = PharmacyType::orderBy('name')->get(['id', 'name']);
        $data['units'] = PharmacyUnit::orderBy('name')->get(['id', 'name']);

        if ($data['edited'] = PharmacyProduct::find($id)) {
            return view('backend.pages.pharmacy_products.edit', $data);
        } else {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('pharmacy_products.edit');

        $rules = [
            'category_id' => ['required', 'integer'],
            'brand_id' => ['required', 'integer'],
            'type_id' => ['required', 'integer'],
            'quantity_type_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'strength' => ['nullable', 'string', 'max:255'],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pharmacy_products', 'barcode')->ignore($id),
            ],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'alert_qty' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ];

        $request->validate($rules);

        try {
            if ($row = PharmacyProduct::find($id)) {
                $row->category_id = $request->category_id;
                $row->brand_id = $request->brand_id;
                $row->type_id = $request->type_id;
                $row->quantity_type_id = $request->quantity_type_id;
                $row->name = $request->name;
                $row->generic_name = $request->generic_name;
                $row->strength = $request->strength;
                $row->barcode = $request->barcode;
                $row->purchase_price = $request->purchase_price;
                $row->sell_price = $request->sell_price;
                $row->alert_qty = $request->alert_qty;
                if (\Illuminate\Support\Facades\Schema::hasColumn('pharmacy_products', 'status')) {
                    $row->status = $request->boolean('status', true) ? 1 : 0;
                }

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Product Updated Successfully');

                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');
            }
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('pharmacy_products.delete');
        $product = PharmacyProduct::find($id);

        if (!$product) {
            return response()->json(['status' => 404, 'message' => 'Product not found.']);
        }

        $hasPurchases = PharmacyPurchaseItem::where('pharmacy_product_id', $id)->exists();
        $hasSales = PharmacySaleItem::where('pharmacy_product_id', $id)->exists();

        if ($hasPurchases || $hasSales) {
            return response()->json([
                'status' => 422,
                'message' => 'Cannot delete: this product has purchase or sale history.',
            ]);
        }

        if ($product->delete()) {
            return response()->json(['status' => 200]);
        }

        return response()->json(['status' => 422, 'message' => 'Failed to delete product.']);
    }
}

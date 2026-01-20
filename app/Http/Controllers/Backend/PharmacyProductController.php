<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\PharmacyBrand;
use App\Models\PharmacyCategory;
use App\Models\PharmacyProduct;
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
        $search = request('search');
        $query = PharmacyProduct::with(['category', 'brand', 'type', 'quantityType'])->orderBy('id', 'DESC');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $data['datas'] = $query->paginate(20);
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
            // Default status to active when creating
            $row->status = 1;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Product Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return $e;
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

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Pharmacy Product Updated Successfully');

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

    public function destroy($id)
    {
        $this->checkOwnPermission('pharmacy_products.delete');
        $deleteData = PharmacyProduct::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}

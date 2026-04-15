<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.products.index";
    public $create_route = "admin.products.create";
    public $store_route = "admin.products.store";
    public $edit_route = "admin.products.edit";
    public $update_route = "admin.products.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Products",
            'sub_title' => "",
            'plural_name' => "products",
            'singular_name' => "Product",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/products'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('products.index');
        $data['pageHeader'] = $this->pageHeader;
        $searchTerm = \request('search');
        $data['datas'] = Product::where('branch_id', auth()->user()->branch_id)
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('code', 'LIKE', '%' . $searchTerm . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('backend.pages.products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('products.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = Category::where('branch_id', auth()->user()->branch_id)
            ->get(['id', 'name']);
        $branchId = auth()->user()->branch_id;

        // Get the latest product code for the same branch
        $latestProduct = Product::where('branch_id', $branchId)->orderBy('code', 'DESC')->first();
        $data['nextCode'] = $latestProduct ? $latestProduct->code + 1 : 1001;
        return view('backend.pages.products.create', $data);
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
        $this->checkOwnPermission('products.create');
        $rules = [
            'category_id' => 'required',
            'name' => 'required|max:200',
            'price' => 'required|numeric|min:0',
            'reefer_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable',
            'code' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($branchId) {
                    if (Product::where('code', $value)->where('branch_id', $branchId)->exists()) {
                        $fail('The product code already exists for this branch.');
                    }
                },
            ],
            'parameters' => 'nullable|array',
            'parameters.*.parameter' => 'required_with:parameters.*.unit,parameters.*.reference_range|nullable|string|max:255',
            'parameters.*.unit' => 'nullable|string|max:100',
            'parameters.*.reference_range' => 'nullable|string|max:255',
        ];
        $request->validate($rules);
        try {
            DB::beginTransaction();

            $row = new Product();
            $row->branch_id = auth()->user()->branch_id;
            $row->category_id = $request->category_id;
            $row->name = $request->name;
            $row->code = $request->code;
            $row->price = $request->price;
            $row->description = $request->description;
            $row->reefer_fee = $request->reefer_fee ?? 0;

            if (!$row->save()) {
                DB::rollBack();
                return RedirectHelper::backWithInput();
            }

            $this->syncProductParameters($row, $request->input('parameters', []));
            DB::commit();

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Product Created Successfully');
        } catch (QueryException $e) {
            DB::rollBack();
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
        $this->checkOwnPermission('products.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = Product::with('parameters')->where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            return view('backend.pages.products.edit', $data);
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
        $this->checkOwnPermission('products.edit');
        $branchId = auth()->user()->branch_id;
        $request->validate([
//                'category_id' => 'required',
            'name' => 'required|max:200',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable',
            'code' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($branchId, $id) {
                    if (Product::where('code', $value)
                        ->where('branch_id', $branchId)
                        ->where('id', '!=', $id) // Ignore current product's ID
                        ->exists()) {
                        $fail('The product code already exists for this branch.');
                    }
                },
            ],
            'parameters' => 'nullable|array',
            'parameters.*.parameter' => 'required_with:parameters.*.unit,parameters.*.reference_range|nullable|string|max:255',
            'parameters.*.unit' => 'nullable|string|max:100',
            'parameters.*.reference_range' => 'nullable|string|max:255',
        ]);
        try {
//               return $request;
            if ($row = Product::where('branch_id', auth()->user()->branch_id)
                ->find($id)) {

                DB::beginTransaction();

//                    $row->category_id = $request->category_id;
                $row->name = $request->name;
                $row->code = $request->code;
                $row->price = $request->price;
                $row->description = $request->description;
                $row->reefer_fee = $request->reefer_fee ?? 0;

                if (!$row->save()) {
                    DB::rollBack();
                    return RedirectHelper::backWithInput();
                }

                $this->syncProductParameters($row, $request->input('parameters', []));
                DB::commit();

                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Product Created Successfully');
            } else {
                return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

            }
        } catch (QueryException $e) {
            DB::rollBack();
            return RedirectHelper::backWithInputFromException();
        }
    }

    private function syncProductParameters(Product $product, array $parameters): void
    {
        $rows = [];

        foreach ($parameters as $item) {
            $parameter = trim((string)($item['parameter'] ?? ''));
            $unit = trim((string)($item['unit'] ?? ''));
            $referenceRange = trim((string)($item['reference_range'] ?? ''));

            if ($parameter === '' && $unit === '' && $referenceRange === '') {
                continue;
            }

            if ($parameter === '') {
                continue;
            }

            $rows[] = [
                'parameter' => $parameter,
                'unit' => $unit !== '' ? $unit : null,
                'reference_range' => $referenceRange !== '' ? $referenceRange : null,
            ];
        }

        $product->parameters()->delete();
        if (!empty($rows)) {
            $product->parameters()->createMany($rows);
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
        $this->checkOwnPermission('products.delete');
        $deleteData = Product::where('branch_id', auth()->user()->branch_id)
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

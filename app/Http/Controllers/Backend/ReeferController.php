<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomPercent;
use App\Models\Reefer;
use App\Services\ReferCommissionService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ReeferController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.reefers.index";
    public $create_route = "admin.reefers.create";
    public $store_route = "admin.reefers.store";
    public $edit_route = "admin.reefers.edit";
    public $update_route = "admin.reefers.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Reefers",
            'sub_title' => "",
            'plural_name' => "reefers",
            'singular_name' => "Reefer",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/reefers'),

        ];
    }

    public function index(Request $request)
    {
        $this->checkOwnPermission('reefers.index');
        $data['pageHeader'] = $this->pageHeader;
        $query = Reefer::with('customParcent')->where('branch_id', auth()->user()->branch_id);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $data['datas'] = $query->orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.reefers.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('reefers.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = Category::where('branch_id', auth()->user()->branch_id)->get();
        return view('backend.pages.reefers.create', $data);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('reefers.create');
        $request->validate([
            'name' => 'required|max:200',
            'percent' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $row = new Reefer();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->phone = $request->phone;
            $row->designation = $request->designation;
            $row->percent = $request->percent;
            $row->type = $request->type;
            $row->office_time = $request->office_time;
            $row->save();

            $this->syncCustomPercents($request, $row);
            DB::commit();

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!</strong> Referrer created successfully.');
        } catch (QueryException $e) {
            DB::rollBack();

            return RedirectHelper::backWithInputFromException();
        }
    }

    public function storeApi(Request $request)
    {
        $this->checkOwnPermission('reefers.create');

        $request->validate([
            'name' => 'required|max:200',
            'percent' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $row = new Reefer();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->phone = $request->phone;
            $row->designation = $request->designation;
            $row->percent = $request->percent;
            $row->type = $request->type;
            $row->office_time = $request->office_time;
            $row->save();

            DB::commit();

            return response()->json([
                'id' => $row->id,
                'name' => $row->name,
                'referID' => $row->id,
                'percent' => (float) $row->percent,
                'has_custom_percent' => false,
                'custom_percents' => [],
            ]);
        } catch (QueryException $e) {
            DB::rollBack();

            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function show($id)
    {
        //
    }

    public function commission(int $id, ReferCommissionService $referCommissionService): JsonResponse
    {
        $this->checkOwnPermission('reefers.index');

        $refer = Reefer::where('branch_id', auth()->user()->branch_id)->find($id);
        if (!$refer) {
            return response()->json(['message' => 'Referrer not found'], 404);
        }

        return response()->json($referCommissionService->referPayload($refer->id));
    }

    public function edit($id)
    {
        $this->checkOwnPermission('reefers.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = Reefer::with('customParcent')->where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $data['categories'] = Category::where('branch_id', auth()->user()->branch_id)->get();

            return view('backend.pages.reefers.edit', $data);
        }

        return RedirectHelper::backWithInputFromException();
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('reefers.edit');

        $request->validate([
            'name' => 'required|max:200',
            'percent' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $row = Reefer::where('branch_id', auth()->user()->branch_id)->find($id);
            if (!$row) {
                DB::rollBack();

                return RedirectHelper::routeError($this->index_route, '<strong>Sorry!</strong> Data not found.');
            }

            $row->name = $request->name;
            $row->phone = $request->phone;
            $row->designation = $request->designation;
            $row->percent = $request->percent;
            $row->type = $request->type;
            $row->office_time = $request->office_time;
            $row->save();

            $this->syncCustomPercents($request, $row);

            DB::commit();

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Updated!</strong> Referrer saved successfully.');
        } catch (QueryException $e) {
            DB::rollBack();

            return RedirectHelper::backWithInputFromException();
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('reefers.delete');
        $deleteData = Reefer::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            }

            return response()->json(['status' => 422]);
        }
    }

    public function customSms()
    {
        $this->checkOwnPermission('reefers.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('type', Reefer::$typeArray[1])->orderBy('id', 'DESC')->get();

        return view('backend.pages.reefers.custom-sms', $data);
    }

    public function customSmsSend(Request $request)
    {
        $message = $request->message;
        $ids = explode(',', $request->selected_ids);
        $reefers = Reefer::whereIn('id', $ids)
            ->whereNotNull('phone')
            ->select('id', 'phone')
            ->get();

        foreach ($reefers as $reefer) {
            smsSent(auth()->user()->branch_id, $reefer->phone, $message);
        }

        return RedirectHelper::back('<strong>Congratulations!</strong> SMS sent successfully.');
    }

    private function syncCustomPercents(Request $request, Reefer $row): void
    {
        $enabled = $request->input('enable_custom_percent') === 'yes';

        CustomPercent::where('refer_id', $row->id)
            ->where('branch_id', $row->branch_id)
            ->delete();

        if (!$enabled) {
            return;
        }

        foreach ($request->input('custom_percent', []) as $categoryId => $percent) {
            if ($percent === null || $percent === '') {
                continue;
            }

            CustomPercent::create([
                'branch_id' => $row->branch_id,
                'refer_id' => $row->id,
                'category_id' => (int) $categoryId,
                'percentage' => (float) $percent,
            ]);
        }
    }
}

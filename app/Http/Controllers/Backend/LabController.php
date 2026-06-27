<?php

namespace App\Http\Controllers\Backend;

use App\Helper\CustomHelper;
use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\Lab;
use App\Models\PurchaseItem;
use App\Models\ReagentTrack;
use App\Models\Setting;
use App\Models\TestReport;
use App\Services\LabFollowupSchemaService;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class LabController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.labs.index";
    public $create_route = "admin.labs.create";
    public $store_route = "admin.labs.store";
    public $edit_route = "admin.labs.edit";
    public $update_route = "admin.labs.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Labs",
            'sub_title' => "",
            'plural_name' => "labs",
            'singular_name' => "Lab",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/labs'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexTest(Request $request)
    {
        $this->checkOwnPermission('labs.index');
        $data['pageHeader'] = $this->pageHeader;
        $query = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->with(['product', 'invoice']);
        if ($request->query('status') == InvoiceList::$statusArray[0]) {
            $query->where('status', $request->get('status'));
        } else if ($request->query('status') == InvoiceList::$statusArray[1]) {
            $query->where('status', InvoiceList::$statusArray[1])
                ->where('admin_id', auth()->id());
        } else if ($request->query('status') == InvoiceList::$statusArray[2]) {
            $query->where('status', $request->get('status'));
            $query->where('admin_id', auth()->id());
        } else {
            $query->where(function ($q) {
                $q->where('status', InvoiceList::$statusArray[0])
                    ->orWhere(function ($q2) {
                        $q2->where('status', InvoiceList::$statusArray[1])
                            ->where('admin_id', auth()->id());
                    });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $query = $query->whereHas('invoice', function ($query) use ($request) {
            if ($request->filled('invoice_number')) {
                $query->where('invoice_number', $request->invoice_number);
            }
        });
        $categoryIds = Lab::where('branch_id', auth()->user()->branch_id)
            ->where('admin_id', auth()->id())
            ->pluck('category_id');
        $query = $query->whereHas('product', function ($query) use ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        });
        $data['datas'] = $query->orderBy('id', 'desc')->paginate(20);

        return view('backend.pages.labs.index', $data);
    }

    public function index(Request $request)
    {
        $this->checkOwnPermission('labs.index');
        $data['pageHeader'] = $this->pageHeader;
        $nowDhaka = Carbon::now('Asia/Dhaka')->toDateString();
        $query = Invoice::with('tests')->withSum('paidAmount', 'paid_amount')
            ->where('branch_id', auth()->user()->branch_id);

        // Date Filtering
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            // Show today's data if no filter is applied
            $query->whereDate('creation_date', $nowDhaka);
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }

        // Invoice number filtering
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', $request->invoice_number);
        }

        $invoiceIds = (clone $query)->pluck('id');
        $data['labStats'] = [
            'invoices' => $invoiceIds->count(),
            'pending_invoices' => (clone $query)->whereHas('tests', fn ($q) => $q->whereIn('status', [
                InvoiceList::$statusArray[0],
                InvoiceList::$statusArray[1],
            ]))->count(),
            'complete_invoices' => (clone $query)->whereDoesntHave('tests', fn ($q) => $q->whereIn('status', [
                InvoiceList::$statusArray[0],
                InvoiceList::$statusArray[1],
                InvoiceList::$statusArray[3],
            ]))->whereHas('tests')->count(),
            'tests_pending' => InvoiceList::where('branch_id', auth()->user()->branch_id)
                ->whereIn('invoice_id', $invoiceIds)->where('status', InvoiceList::$statusArray[0])->count(),
            'tests_processing' => InvoiceList::where('branch_id', auth()->user()->branch_id)
                ->whereIn('invoice_id', $invoiceIds)->where('status', InvoiceList::$statusArray[1])->count(),
            'tests_complete' => InvoiceList::where('branch_id', auth()->user()->branch_id)
                ->whereIn('invoice_id', $invoiceIds)->where('status', InvoiceList::$statusArray[2])->count(),
        ];
        $data['datas'] = $query->orderBy('id', 'desc')->paginate(30);

        return view('backend.pages.labs.index-invoice', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('labs.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.labs.create', $data);
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
        $this->checkOwnPermission('labs.create');
        $rules = [
            'name' => 'required|max:200',
        ];
        $request->validate($rules);
        try {
            $row = new Lab();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Lab Created Successfully');

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
        $this->checkOwnPermission('labs.index');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['singleData'] = Invoice::with(['invoiceList.product'])
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $data['reports'] = TestReport::with(['invoiceItem.product'])
                ->where('invoice_id', $id)
                ->get();
            $data['purchaseItems'] = PurchaseItem::with('item')
                ->where('branch_id', auth()->user()->branch_id)
                ->whereColumn('quantity', '>', 'quantity_spend')
                ->orderBy('expiry_date')
                ->get();
            $data['followupSchemaReady'] = app(LabFollowupSchemaService::class)->isInstalled();
            $data['upcomingFollowups'] = $data['singleData']->invoiceList
                ->filter(fn ($line) => !empty($line->followup_date))
                ->sortBy('followup_date')
                ->values();
            return view('backend.pages.labs.show', $data);
        } else {
            return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

        }
        return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkOwnPermission('labs.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = InvoiceList::with(['product', 'invoice'])
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            return view('backend.pages.labs.edit', $data);
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
        $this->checkOwnPermission('labs.edit');

        $request->validate([
            'description' => 'nullable',
            'file' => 'nullable|mimes:jpg,png,pdf,doc,docx,xlsx,csv|max:300',
        ]);

//        try {
        if ($row = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $row->test_report = $request->description;
            $row->status = InvoiceList::$statusArray[2];
            $row->admin_id = auth()->id();
            if ($request->hasFile('file')) {
                CustomHelper::deleteFile('images/' . auth()->user()->branch_id . '/' . $row->document);
                $logoPath = CustomHelper::imageUpload($request->file('file'), 'test-reports' . auth()->user()->branch_id);
                $row->document = $logoPath;
            }
            if ($row->save()) {
                return RedirectHelper::routeSuccessWithSubParam($this->index_route, ['status' => \request('status')], '<strong>Congratulations!!!</strong> Lab Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
        } else {
            return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

        }
//        } catch (QueryException $e) {
//            return $e;
//            return RedirectHelper::backWithInputFromException();
//        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->checkOwnPermission('labs.delete');
        $deleteData = Lab::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }

    public function labTestStatus($id)
    {
        $this->checkOwnPermission('labs.create');
        $row = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->find($id);
        if (InvoiceList::$statusArray[0] == $row->status) {
            $newStatus = InvoiceList::$statusArray[1];
        } else if (InvoiceList::$statusArray[1] == $row->status) {
            $newStatus = InvoiceList::$statusArray[2];
        } else {
            $newStatus = $row->status;
        }
        $row->status = $newStatus;
        $row->admin_id = auth()->id();
        if (($row->save())) {
            return response()->json(['status' => 200]);
        } else {
            return response()->json(['status' => 422]);

        }
    }

    public function reportPdfPreview($id)
    {
        $data['pageHeader'] = $this->pageHeader;
        $data['invoice'] = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->with('invoice')->find($id);
        $qrCode = new QrCode($data['invoice']->id);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $data['qrcode'] = base64_encode($result->getString());
        return view('backend.pages.labs.report-pdf', $data);
    }

    public function updateItem(Request $request, $invoiceItemId)
    {
        $this->checkOwnPermission('labs.edit');

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'required|integer|exists:purchase_items,id',
        ]);

        InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->findOrFail($invoiceItemId);

        $branchId = auth()->user()->branch_id;
        $adminId = auth()->id();

        try {
            DB::transaction(function () use ($request, $invoiceItemId, $branchId, $adminId) {
                foreach ($request->items as $purchaseItemId) {
                    $row = PurchaseItem::with('item')
                        ->where('branch_id', $branchId)
                        ->where('id', $purchaseItemId)
                        ->lockForUpdate()
                        ->first();

                    if (!$row) {
                        throw new \RuntimeException('Selected reagent is not available for this branch.');
                    }

                    if ($row->quantity_spend >= $row->quantity) {
                        $name = $row->item->name ?? 'Reagent';
                        throw new \RuntimeException("Insufficient stock for {$name}. Remaining: 0");
                    }

                    $row->quantity_spend = $row->quantity_spend + 1;
                    $row->save();

                    ReagentTrack::create([
                        'admin_id' => $adminId,
                        'branch_id' => $branchId,
                        'purchase_item_id' => $purchaseItemId,
                        'invoice_list_id' => $invoiceItemId,
                    ]);
                }
            });

            return RedirectHelper::back('<strong>Success!</strong> Reagent added successfully.');
        } catch (\RuntimeException $exception) {
            return RedirectHelper::backWithInputFromException($exception->getMessage());
        } catch (\Throwable $exception) {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function updateInvoiceItemFollowup(Request $request, $invoiceItemId)
    {
        if (!auth('admin')->user()?->can('labs.edit') && !auth('admin')->user()?->can('labs.index')) {
            abort(403);
        }

        if (!app(LabFollowupSchemaService::class)->isInstalled()) {
            return RedirectHelper::backWithInputFromException('Lab follow-up columns are not installed. Apply System Updates → Lab Follow-up Notes.');
        }

        $request->validate([
            'note' => 'nullable|string|max:1000',
            'followup_date' => 'nullable|date',
        ]);

        $invoiceItem = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->findOrFail($invoiceItemId);

        $invoiceItem->note = $request->note;
        $invoiceItem->followup_date = $request->followup_date ?: null;
        $invoiceItem->save();

        return RedirectHelper::back('<strong>Updated!</strong> Test note and follow-up date saved successfully.');
    }

    public function DeleteReagetntTrack($id)
    {
        $this->checkOwnPermission('labs.delete');

        $deleteData = ReagentTrack::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (is_null($deleteData)) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!</strong> Reagent record not found.');
        }

        try {
            DB::transaction(function () use ($deleteData) {
                $row = PurchaseItem::where('branch_id', auth()->user()->branch_id)
                    ->where('id', $deleteData->purchase_item_id)
                    ->lockForUpdate()
                    ->first();

                if (!$deleteData->delete()) {
                    throw new \RuntimeException('Could not delete reagent record.');
                }

                if ($row && $row->quantity_spend > 0) {
                    $row->quantity_spend = $row->quantity_spend - 1;
                    $row->save();
                }
            });

            return RedirectHelper::back('<strong>Success!</strong> Reagent removed and stock restored.');
        } catch (\Throwable $exception) {
            return RedirectHelper::backWithInputFromException();
        }
    }


}

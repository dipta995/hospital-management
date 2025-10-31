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
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

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
            $query->where('created_at', '>=', $request->end_date);
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
        // Clone the query before executing
        $queryTotal = clone $query;
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
        if ($data['singleData'] = Invoice::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $data['reports'] = TestReport::where('invoice_id', $id)->get();
            $data['purchaseItems'] = PurchaseItem::where('branch_id', auth()->user()->branch_id)
                ->whereColumn('quantity', '>', 'quantity_spend')
                ->get()->unique('item_id')
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
        if ($data['edited'] = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->where('admin_id', auth()->id())
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
        foreach ($request->items as $item) {
            $row = PurchaseItem::find($item);
//    $row->quantity = $request->quantity;
            $row->quantity_spend = $row->quantity_spend + 1;
            $row->save();
            $data = new ReagentTrack();
            $data->admin_id = auth()->id();
            $data->branch_id = auth()->user()->branch_id;
            $data->purchase_item_id = $item;
            $data->invoice_list_id = $invoiceItemId;
            $data->save();
        }
        return RedirectHelper::back();


    }

    public function DeleteReagetntTrack($id)
    {
        $this->checkOwnPermission('labs.delete');
       $deleteData = ReagentTrack::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                $row = PurchaseItem::find($deleteData->purchase_item_id);
                $row->quantity_spend = $row->quantity_spend - 1;
                $row->save();
                return RedirectHelper::back('<strong>Congratulations!!!</strong> Reagent delete Successfully');
            } else {
                return RedirectHelper::backWithInput();
            }
        }
    }


}

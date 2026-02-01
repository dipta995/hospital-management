<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\TestReport;
use App\Models\TestReportDemo;
use App\Models\Invoice;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class TestReportController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.test_reports.index";
    public $create_route = "admin.test_reports.create";
    public $store_route = "admin.test_reports.store";
    public $edit_route = "admin.test_reports.edit";
    public $update_route = "admin.test_reports.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Test Reports",
            'sub_title' => "",
            'plural_name' => "test_reports",
            'singular_name' => "TestReport",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/test_reports'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->checkOwnPermission('test_reports.index');
        $data['pageHeader'] = $this->pageHeader;
        $data['datas'] = TestReport::orderBy('id', 'DESC')->paginate(10);
        return view('backend.pages.test_reports.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('test_reports.create');
        $data['pageHeader'] = $this->pageHeader;
        $invoiceId = request('invoiceId');
        $data['invoiceId'] = $invoiceId;
        $data['testReport'] = request('testReport');
        // When an invoice is provided, always show that invoice's tests
        // on the left, and attach any matching templates from TestReportDemo.
        if ($invoiceId) {
            $invoice = Invoice::with(['invoiceList.product.category'])
                ->where('branch_id', auth()->user()->branch_id)
                ->find($invoiceId);

            $data['invoice'] = $invoice;

            if ($invoice) {
                $allTemplates = TestReportDemo::all();

                $data['reportDemo'] = $invoice->invoiceList->map(function ($item) use ($allTemplates) {
                    $product = $item->product;
                    if (!$product) {
                        return null;
                    }

                    $name = $product->name; // e.g. "CBC(1002)"
                    $normalized = preg_replace('/\\s*\([^)]*\)$/', '', $name); // e.g. "CBC"

                    // Prefer any existing saved report on the invoice item,
                    // otherwise fall back to the product's default description.
                    $baseReport = $item->test_report ?? $product->description ?? '';

                    $categoryName = optional($product->category)->name ?? 'Others';

                    return (object) [
                        'id' => $item->id,
                        'name' => $name,
                        'type' => $categoryName,
                        'test_report' => $baseReport,
                    ];
                })->filter()->values();
            } else {
                // If invoice not found, fall back to all templates
                $data['reportDemo'] = TestReportDemo::all();
            }
        } else {
            $data['invoice'] = null;
            $data['reportDemo'] = TestReportDemo::all();
        }

        if (request()->query('testReport')) {
            $data['edited'] = TestReportDemo::findOrFail(request()->query('testReport'));
        }
        return view('backend.pages.test_reports.create', $data);
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
        $this->checkOwnPermission('test_reports.create');
        $rules = [
//            'name' => 'required|max:200',
            'report' => 'required',
        ];
        $request->validate($rules);
        try {
            $row = new TestReport();
            $row->admin_id = auth()->id();
            $row->invoice_id = $request->invoiceId;
            $row->test_report_id = $request->testReport;
            $row->report = $request->report;

            if ($row->save()) {
                return RedirectHelper::routeSuccessWithSubParam('admin.labs.show',$request->invoiceId, '<strong>Congratulations!!!</strong> TestReport Created Successfully');

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
        $this->checkOwnPermission('test_reports.edit');
        $data['pageHeader'] = $this->pageHeader;
        if($data['edited'] = TestReport::find($id)) {
        return view('backend.pages.test_reports.edit', $data);
        }else{
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
        $this->checkOwnPermission('test_reports.edit');
            $request->validate([
                'report' => 'required',
            ]);
            try {
                if($row = TestReport::find($id)){
                    $row->report = $request->report;
                    if ($row->save()) {
                    return RedirectHelper::routeSuccessWithSubParam('admin.labs.show',$row->invoice_id, '<strong>Congratulations!!!</strong> TestReport Created Successfully');

                } else {
                    return RedirectHelper::backWithInput();
                }
                }else{
                    return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

                }
            } catch (QueryException $e) {
                return $e;
                return RedirectHelper::backWithInputFromException();
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
        $this->checkOwnPermission('test_reports.delete');
        $deleteData = TestReport::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }

    public function reportPdfPreview($id)
    {
        $data['pageHeader'] = $this->pageHeader;
        $data['invoice'] = TestReport::with('invoice')->find($id);
        $qrCode = new QrCode($data['invoice']->id);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $data['qrcode'] = base64_encode($result->getString());
        return view('backend.pages.invoices.report-pdf', $data);
    }
    public function reportPdfdelete($id)
    {
        $deleteData = TestReport::find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return back();
            } else {
                return RedirectHelper::backWithInputFromException();
            }
        }
    }
}

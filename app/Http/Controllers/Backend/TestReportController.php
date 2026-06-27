<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\TestReport;
use App\Models\Invoice;
use App\Models\InvoiceList;
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
     * Lab staff use this flow from the Lab page; allow labs.edit as well as test_reports.* permissions.
     */
    protected function checkTestReportPermission(string $testReportPermission, ?string $labPermission = 'labs.edit'): void
    {
        if ($this->user->can($testReportPermission)) {
            return;
        }

        if ($labPermission && $this->user->can($labPermission)) {
            return;
        }

        abort(403, 'Unauthorized Access');
    }

    protected function syncInvoiceListReport(?int $invoiceListId, string $reportHtml): void
    {
        if (!$invoiceListId) {
            return;
        }

        InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->where('id', $invoiceListId)
            ->update(['test_report' => $reportHtml]);
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
        $this->checkTestReportPermission('test_reports.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['invoiceId'] = request('invoiceId');
        $data['testReport'] = request('testReport');

        if ($data['invoiceId']) {
            $data['invoice'] = Invoice::where('branch_id', auth()->user()->branch_id)
                ->find($data['invoiceId']);

            $data['tests'] = $data['invoice']
                ? InvoiceList::with(['product.category', 'product.parameters'])
                    ->where('invoice_id', $data['invoiceId'])
                    ->get()
                : collect();
        } else {
            $data['invoice'] = null;
            $data['tests'] = collect();
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
        $this->checkTestReportPermission('test_reports.create');
        $rules = [
            'report' => 'required',
        ];
        if ($request->filled('invoiceId')) {
            $rules['testReport'] = 'required|integer';
        }
        $request->validate($rules);
        try {
            $row = new TestReport();
            $row->admin_id = auth()->id();
            $row->invoice_id = $request->invoiceId;
            $row->test_report_id = $request->testReport;
            $row->report = $request->report;

            if ($row->save()) {
                $this->syncInvoiceListReport((int) $request->testReport, (string) $request->report);
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
        $this->checkTestReportPermission('test_reports.edit');
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
        $this->checkTestReportPermission('test_reports.edit');
            $request->validate([
                'report' => 'required',
            ]);
            try {
                if($row = TestReport::find($id)){
                    $row->report = $request->report;
                    if ($row->save()) {
                    $this->syncInvoiceListReport((int) $row->test_report_id, (string) $request->report);
                    return RedirectHelper::routeSuccessWithSubParam('admin.labs.show',$row->invoice_id, '<strong>Congratulations!!!</strong> TestReport Updated Successfully');

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
        $this->checkTestReportPermission('test_reports.index', 'labs.index');
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

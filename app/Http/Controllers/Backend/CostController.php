<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\CostCategory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Reefer;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class CostController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.costs.index";
    public $create_route = "admin.costs.create";
    public $store_route = "admin.costs.store";
    public $edit_route = "admin.costs.edit";
    public $update_route = "admin.costs.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Costs",
            'sub_title' => "",
            'plural_name' => "costs",
            'singular_name' => "Cost",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/costs'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->checkOwnPermission('costs.index');
        $data['pageHeader'] = $this->pageHeader;
        $nowDhaka = Carbon::now('Asia/Dhaka');
        $query = Cost::query();
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $query->whereDate('creation_date', $nowDhaka->toDateString());
        } else {
            // Filter by date range if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('creation_date', '<=', $request->end_date);
            }
        }
        $costQuery = $query->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'desc');
        if ($request->query('export') == 'pdf') {
            $data['datas'] = $costQuery->get();
        } else {
            $data['datas'] = $costQuery->paginate(10);
        }
        $data['totalAmount'] = $query->where('branch_id', auth()->user()->branch_id)
            ->sum('amount');
        if ($request->query('export') == 'pdf') {
            return view('backend.pages.costs.export-pdf', $data);
        }
        return view('backend.pages.costs.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('costs.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = CostCategory::where('branch_id', auth()->user()->branch_id)->get();
        return view('backend.pages.costs.create', $data);
    }

    public function createMultiple(Request $request)
    {
        $this->checkOwnPermission('costs.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.costs.create', $data);
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
        $this->checkOwnPermission('costs.create');
        $rules = [
            'amount' => 'required|numeric|min:1',
        ];
        $request->validate($rules);
        try {
            $row = new Cost();
            $row->branch_id = auth()->user()->branch_id;
            $row->admin_id = auth()->id();
            $row->cost_category_id = $request->cost_category_id;
            if ($request->account_no) {
                $row->reason = $request->reason . "(account-" . $request->account_no . ")";
            } else {
                $row->reason = $request->reason;
            }
            $row->amount = $request->amount;
            $row->invoice_id = $request->invoice_id ?? null;
            $row->refer_id = $request->refer_id ?? null;
            $row->account_details = $request->account_details ?? null;
            $row->account_type = $request->account_type ?? null;
            $row->payment_type = $request->payment_type ?? Cost::$paymentArray[0];
            $row->creation_date = $request->date ?? Carbon::now('Asia/Dhaka')->format('Y-m-d');
//            dd($request->pc_payment);
            if ($request->pc_payment == 'pc_payment') {
                if (Setting::get('pc_payment_sms') == 'Yes') {
                    if ($request->refer_id) {
                        $ref = Reefer::find($request->refer_id);
                        $message = "{$request->amount} টাকা পরিশোধ হয়েছে। - " . Setting::get('company_name');
                        smsSent(auth()->user()->branch_id, $ref->phone, $message);

                    }
                }
            }
            if ($row->save()) {

                return RedirectHelper::back('<strong>Congratulations!!!</strong> Cost Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

    }


    public function storeMultiple(Request $request)
    {
//        return $request;
        $this->checkOwnPermission('costs.create');
//        $rules = [
//            'amount' => 'required|numeric|min:1',
//        ];
//        $request->validate($rules);
        try {
            $totalAmount = 0;
            foreach ($request->invoices as $item) {
                $row = new Cost();
                $row->branch_id = auth()->user()->branch_id;
                $row->admin_id = auth()->id();
//            $row->cost_category_id = $item->cost_category_id;
//            if ($item->account_no) {
//                $row->reason = "(Refer Paymen account-" . $item->account_no . ")";
//            } else {
                $row->reason = "Refer Paymen (".Invoice::find($item['invoice_id'])->invoice_number."-".Invoice::find($item['invoice_id'])->creation_date.")".Reefer::find($item['refer_id'])->name;
//            }
                $row->invoice_id = $item['invoice_id'];
                $row->amount = $item['amount'];
                $row->refer_id = $item['refer_id'] ?? null;
                $row->payment_type = $request->payment_type ?? Cost::$paymentArray[0];
                $row->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
                if (!Cost::where('invoice_id', $item['invoice_id'])
                        ->where('refer_id', $item['refer_id'])
                        ->where('amount', $item['amount'])
                        ->where('creation_date', Carbon::now('Asia/Dhaka')->format('Y-m-d'))
                        ->count() > 0) {
                    $row->save();
                }
                $totalAmount += $item['amount'];
            }

            if (Setting::get('pc_payment_sms') == 'Yes') {
                if ($request->refer_id) {
                    $ref = Reefer::find($request->refer_id);
                    $format = Setting::get('refer_payment_sms_format');
                    $message = str_replace(
                        ['{amount}'],
                        [
                            $totalAmount,
                        ],
                        $format
                    );
                    smsSent(auth()->user()->branch_id, $ref->phone, $message);

                }
            }
            return response()->json(['message' => 'Invoices paid successfully.']);


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
        $this->checkOwnPermission('costs.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['categories'] = CostCategory::where('branch_id', auth()->user()->branch_id)->get();
        if ($data['edited'] = Cost::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            return view('backend.pages.costs.edit', $data);
            if (\Carbon\Carbon::parse($data['edited']->created_at)->setTimezone('Asia/Dhaka')->isToday()) {
            } else {
                return RedirectHelper::backWithInputFromException();
            }
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
        $this->checkOwnPermission('costs.edit');

        $request->validate([
            'cost_category_id' => 'required',
        ]);
        try {
            if ($row = Cost::where('branch_id', auth()->user()->branch_id)
                ->find($id)) {
                $row->cost_category_id = $request->cost_category_id;
                $row->reason = $request->reason;
                $row->amount = $request->amount;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Cost Created Successfully');

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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

       public function destroy($id)
    {
        $this->checkOwnPermission('costs.delete');

        $deleteData = Cost::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!$deleteData) {
            return response()->json(['status' => 404, 'message' => 'Cost not found']);
        }

        if (!empty($deleteData->salary_id)) {
            $salary = \App\Models\EmployeeSalary::where('id', $deleteData->salary_id)->first();
            if ($salary) {
                $salary->delete();
            }
        }

        if ($deleteData->employee) {
            $employee = $deleteData->employee;
            $employee->total_costs = max(0, $employee->total_costs - $deleteData->amount);
            $employee->save();
        }

        if ($deleteData->payment_id) {
            $payment = \App\Models\Payment::find($deleteData->payment_id);
            if ($payment) {
                $payment->delete();
            }
        }

        if ($deleteData->delete()) {
            return response()->json([
                'status' => 200,
                'message' => 'Cost and related salary deleted successfully!',
            ]);
        }
        return response()->json(['status' => 422, 'message' => 'Unable to delete cost']);
    }





}

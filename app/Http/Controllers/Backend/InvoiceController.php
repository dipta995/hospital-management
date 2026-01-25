<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\CustomPercent;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\InvoicePayment;
use App\Models\Product;
use App\Models\Reefer;
use App\Models\Setting;
use App\Models\TestReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class InvoiceController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.invoices.index";
    public $create_route = "admin.invoices.create";
    public $store_route = "admin.invoices.store";
    public $edit_route = "admin.invoices.edit";
    public $update_route = "admin.invoices.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Invoices",
            'sub_title' => "",
            'plural_name' => "invoices",
            'singular_name' => "Invoice",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/invoices'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->checkOwnPermission('invoices.index');
        $data['pageHeader'] = $this->pageHeader;
        $nowDhaka = Carbon::now('Asia/Dhaka')->toDateString();

        // Start building the query
        $query = Invoice::withSum('paidAmount', 'paid_amount')
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
        // Invoice number customer
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // Invoice number Admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        } // Invoice number Admin
        if ($request->filled('dr_refer_id')) {
            $query->where('dr_refer_id', $request->dr_refer_id);
        }
        if ($request->has('due')) {
            if ($request->due === 'yes') {
                // Only show invoices with due amount
                $query->whereRaw('total_amount > (
                    SELECT COALESCE(SUM(paid_amount), 0)
                    FROM invoice_payments
                    WHERE invoice_payments.invoice_id = invoices.id
                )');
            } elseif ($request->due === 'no') {
                // Only show fully paid invoices
                $query->whereRaw('total_amount <= (
                    SELECT COALESCE(SUM(paid_amount), 0)
                    FROM invoice_payments
                    WHERE invoice_payments.invoice_id = invoices.id
                )');
            }
        }


        // Clone the query before executing
        $queryTotal = clone $query;

        // Get paginated results
        $data['datas'] = $query->orderBy('id', 'desc')->paginate(30);

        // Calculate summary amounts **only for filtered invoices**
        $totalCollection = $queryTotal->sum('total_amount');
        $discountAmount = $queryTotal->sum('discount_amount');


        $query = InvoicePayment::with(['invoice.invoiceList'])
            ->whereHas('invoice', function ($q) use ($request) {
                $q->where('branch_id', auth()->user()->branch_id);
                if ($request->filled('admin_id')) {
                    $q->where('admin_id', $request->admin_id);
                }
                if ($request->filled('invoice_number')) {
                    $q->where('invoice_number', $request->invoice_number);
                }
                // Invoice number Admin
                if ($request->filled('dr_refer_id')) {
                    $q->where('dr_refer_id', $request->dr_refer_id);
                }
                if ($request->has('due')) {
                    if ($request->due === 'yes') {
                        // Only show invoices with due amount
                        $q->whereRaw('total_amount > (
                    SELECT COALESCE(SUM(paid_amount), 0)
                    FROM invoice_payments
                    WHERE invoice_payments.invoice_id = invoices.id
                )');
                    } elseif ($request->due === 'no') {
                        // Only show fully paid invoices
                        $q->whereRaw('total_amount <= (
                    SELECT COALESCE(SUM(paid_amount), 0)
                    FROM invoice_payments
                    WHERE invoice_payments.invoice_id = invoices.id
                )');
                    }
                }
            });
        // Apply date filters
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
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


        $totalPaidAmount = $query->sum('paid_amount');
        if ($request->filled('admin_id')) {
            $adminId = $request->admin_id;
        } else {
            $adminId = auth()->id();
        }
        $otherPaymentsQuery = InvoicePayment::where('branch_id', auth()->user()->branch_id);
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $otherPaymentsQuery->whereDate('creation_date', $nowDhaka);
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $otherPaymentsQuery->whereBetween('creation_date', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $otherPaymentsQuery->where('creation_date', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $otherPaymentsQuery->where('creation_date', '<=', $request->end_date);
            }
        }
        $ownPaymentsQuery = clone $otherPaymentsQuery;
        $receivedByCurrentAdmin = $ownPaymentsQuery->where('admin_id', $adminId)
            ->sum('paid_amount');
        $receivedByOthers = $otherPaymentsQuery->where('admin_id', '!=', $adminId)->sum('paid_amount');

        // Store summary data
        $data['totalAmount'] = $totalCollection;
        $data['discount_amount'] = $discountAmount;
        $data['total_paid_amount'] = $totalPaidAmount;
        $data['total_due_amount'] = $totalCollection - $totalPaidAmount;
        $data['my_collection'] = $receivedByCurrentAdmin;
        $data['other_collection'] = $receivedByOthers;
        $data['reffers'] = Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('type', Reefer::$typeArray[0])->get();
        return view('backend.pages.invoices.index', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateInvoiceNumber()
    {
        $latestInvoice = Invoice::where('branch_id', auth()->user()->branch_id)
            ->whereYear('creation_date', Carbon::now('Asia/Dhaka')->year)
            ->whereMonth('creation_date', Carbon::now('Asia/Dhaka')->month)
            ->latest('invoice_number')
            ->first();
//dd($latestInvoice);
        if ($latestInvoice) {
            $lastNumber = (int)substr($latestInvoice->invoice_number, 4);
            $invoiceNumber = 'INV-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $invoiceNumber = 'INV-0001';
        }
        return $invoiceNumber;
    }


    public function create()
    {
        $this->checkOwnPermission('invoices.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['invoice_number'] = $this->generateInvoiceNumber();
        if($data['user_data'] = User::find(request('for'))){
        return view('backend.pages.invoices.create', $data);
        }else{
        return view('backend.pages.invoices.create-direct', $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//         $data['products'] = $request->products ;
//         $data['customerDetails'] = $request->customerDetails ;
//         $data['refs'] = $request->refs ;
//         $data['paymentDetails'] = $request->paymentDetails ;
//         return $data;
        $this->checkOwnPermission('invoices.create');

        try {
            $rules = [
//                'products' => 'required|array',
//                'products.*.product_id' => 'required|integer',
//                'products.*.price' => 'required|numeric',
//                'products.*.ref_amount' => 'required|numeric',
//            'customerDetails.name' => 'required|string|max:255',
//            'customerDetails.patient_age_year' => 'required|max:3',
//            'customerDetails.phone' => 'required|string',
//            'paymentDetails.final_amount' => 'required|numeric',
            ];
            $request->validate($rules);
            if (Invoice::where('branch_id', auth()->user()->branch_id)->where('patient_no', self::getNextPatientNo())->exists()) {
                return response()->json(['error' => 401]);
            }
            if (!Invoice::where('branch_id', auth()->user()->branch_id)
                ->where('invoice_number', $request->invoice_number)
                ->where('patient_no', self::getNextPatientNo())->exists()) {
                $invoiceId = \DB::transaction(function () use ($rules, $request) {
                    if ($request['customerDetails']['for'] ===null){
                        $cust = new User();
                        $cust->name = $request['customerDetails']['patient_name'];
                        $cust->age = $request['customerDetails']['patient_age_year'];
                        $cust->phone = $request['customerDetails']['patient_phone'];
                        $cust->email = $request['customerDetails']['patient_email'] ?? null;
                        $cust->gender = $request['customerDetails']['patient_gender'];
                        $cust->blood_group = $request['customerDetails']['patient_blood_group'];
                        $cust->address = $request['customerDetails']['patient_address'];
                        $cust->save();
                        $userID = $cust->id;
                    }else{
                        $userID =  $request['customerDetails']['for'];
                    }
                    $row = new Invoice();
                    $row->user_id = $userID;
                    $row->branch_id = auth()->user()->branch_id;
                    $row->patient_no = self::getNextPatientNo();
                    $row->dr_refer_id = $request['customerDetails']['dr_refer_id'];
                    $row->dr_name = $request['customerDetails']['dr_refer_name'];
                    $row->refer_id = $request['customerDetails']['refer_id'];
                    $row->admin_id = auth()->id();
                    $row->invoice_number = self::generateInvoiceNumber();
                    $row->discount_by = $request['customerDetails']['discount_by'];
                    $row->total_amount = $request['paymentDetails']['total_amount'];
                    $row->discount_amount = $request['paymentDetails']['discount_amount'];
                    $refer = Reefer::find($request['customerDetails']['refer_id']);
                    if ($refer && $refer->customParcent->isNotEmpty()) {
                        $refFeeTotal =  $this->refferAmountNew($request['customerDetails']['refer_id'], $request['products'], $request['paymentDetails']['discount_amount']);
                    }else{
                       $refFeeTotal = self::refferAmount($request['customerDetails']['refer_id'], ($request['paymentDetails']['total_amount'] + $request['paymentDetails']['discount_amount']), $request['paymentDetails']['discount_amount']);
                    }
                    if ($request['customerDetails']['refer_id'] != null) {
                        $row->refer_fee_total =$refFeeTotal;

                    } else {
                        $row->refer_fee_total = 0.00;
                    }
                    $row->refer_fee_total_agent = $request->refer_fee_total_agent ?? 0.00;
//                $row->discount_percent = $request->discount_percent;
                    $row->delivery_at = $request['customerDetails']['delivery_at'];
                    $row->payment_type = $request['paymentDetails']['payment_type'] ?? 'Cash';
                    $row->patient_name = $request['customerDetails']['patient_name'];
                    $row->patient_age_year = $request['customerDetails']['patient_age_year'];
                    $row->patient_phone = $request['customerDetails']['patient_phone'];
                    $row->patient_email = $request['customerDetails']['patient_email'] ?? null;
                    $row->patient_gender = $request['customerDetails']['patient_gender'];
                    $row->patient_blood_group = $request['customerDetails']['patient_blood_group'];
                    $row->patient_address = $request['customerDetails']['patient_address'];
                    $row->creation_date = $request['customerDetails']['creation_date'] ?? Carbon::now('Asia/Dhaka')->format('Y-m-d');
//                $row->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
                    $row->save();
                    $duePaid = new InvoicePayment();
                    $duePaid->paid_amount = $request['paymentDetails']['paid_amount'];
                    $duePaid->invoice_id = $row->id;
                    $duePaid->branch_id = auth()->user()->branch_id;
                    $duePaid->admin_id = auth()->id();
                    $duePaid->creation_date = $request['customerDetails']['creation_date'] ?? Carbon::now('Asia/Dhaka')->format('Y-m-d');
//                $duePaid->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
                    $duePaid->save();
//                return $row;
                    // Save products
                    $totalProducts = count($request['products']);
                    foreach ($request['products'] as $product) {
                        InvoiceList::create([
                            'branch_id' => auth()->user()->branch_id,
                            'invoice_id' => $row->id,
//                        'admin_id' => auth()->id(),
                            'product_id' => $product['product_id'],
                            'price' => $product['price'],
                            'discount_price' => ($row->discount_amount / $totalProducts),
//                        'refer_fee' => $product['ref_amount'],
                        ]);
                    }
//                return response()->json(['invoice_id' => $row->id]);
                    return $row;
                });
                if (Setting::get('invoice_sms') == 'Yes') {
                    $ref = Reefer::find($request['customerDetails']['refer_id']);
                    $refDr = Reefer::find($request['customerDetails']['dr_refer_id']);

                    $format = Setting::get('invoice_customer_sms_format')
                        ?: 'আমাদের হাসপাতালে আসার জন্য আপনাকে ধন্যবাদ । আইডি {invoice_number} বাকি {due_amount} এডভান্স {advance_amount} ২৪ঘন্টা  সেবায় আমরা আছি আপনার পাশে।';
                    $formatDr = Setting::get('invoice_doctor_sms_format')
                        ?: 'আমাদের হাসপাতালে রুগি পাঠানোর জন্য অনেক ধন্যবাদ। রুগির নাম {patient_name}, আইডি {invoice_number} । আপনার সহযোগিতা সর্বদা কামনা করি।';
                    $formatRf = Setting::get('invoice_doctor_refer_sms_format') ?: $formatDr;

                    $dueAmount = $invoiceId->total_amount - $invoiceId->paidAmount->sum('paid_amount');
                    $advanceAmount = $invoiceId->paidAmount->sum('paid_amount');

                    // Customer SMS
                    if ($ref) {
                        $messagePatient = str_replace(
                            [
                                '{due_amount}',
                                '{patient_name}',
                                '{ref_name}',
                                '{invoice_number}',
                                '{advance_amount}'
                            ],
                            [
                                $dueAmount,
                                $invoiceId->patient_name,
                                $ref->name,
                                $invoiceId->invoice_number,
                                $advanceAmount
                            ],
                            $format
                        );
                    } else {
                        $messagePatient = str_replace(
                            [
                                '{due_amount}',
                                '{patient_name}',
                                '{ref_name}',
                                '{invoice_number}',
                                '{advance_amount}'
                            ],
                            [
                                $dueAmount,
                                $invoiceId->patient_name,
                                '',
                                $invoiceId->invoice_number,
                                $advanceAmount
                            ],
                            $format
                        );
                    }

                    // Referer SMS
                    if ($ref) {
                        $messageRef = str_replace(
                            [
                                '{amount}',
                                '{patient_name}',
                                '{dr_name}',
                                '{invoice_number}'
                            ],
                            [
                                $request['paymentDetails']['total_amount'],
                                $invoiceId->patient_name,
                                $ref->name,
                                $invoiceId->invoice_number
                            ],
                            $formatRf
                        );
                    }

                    // Doctor SMS
                    if ($refDr) {
                        $messageDr = str_replace(
                            [
                                '{amount}',
                                '{patient_name}',
                                '{dr_name}',
                                '{invoice_number}'
                            ],
                            [
                                $request['paymentDetails']['total_amount'],
                                $invoiceId->patient_name,
                                $refDr->name,
                                $invoiceId->invoice_number
                            ],
                            $formatDr
                        );
                    }

                    // Send to customer
                    if (isset($request['customerDetails']['patient_phone']) &&
                        preg_match('/^\d{11}$/', $request['customerDetails']['patient_phone'])) {
                        smsSent(auth()->user()->branch_id, $request['customerDetails']['patient_phone'], $messagePatient);
                    }

                    // Send to referer
                    if (isset($messageRef) && $ref && preg_match('/^\d{11}$/', $ref->phone)) {
                        smsSent(auth()->user()->branch_id, $ref->phone, $messageRef);
                    }

                    // Send to doctor
                    if (isset($messageDr) && $refDr && preg_match('/^\d{11}$/', $refDr->phone)) {
                        smsSent(auth()->user()->branch_id, $refDr->phone, $messageDr);
                    }
                }
//            dd($invoiceId);
                return response()->json(
                    [
                        'invoice_id' => $invoiceId->id,
                        'customer_name' => $invoiceId->patient_name,
                    ]
                );

            } else {
                return response()->json(['error' => 400]);
            }
        } catch (QueryException $e) {
            return $e;
//            return RedirectHelper::backWithInputFromException();
            return response()->json(['error' => 400]);

        }

    }

    protected function refferAmountNew($referId, $products, $discount)
    {
        $amount = 0;
        foreach ($products as $product) {
            $productPrice = $product['price'];
            $paecentage = $this->referOtherPercentage($referId, $product['product_id']);
            $amount += ($paecentage * $productPrice) / 100;
        }
        return ($amount > 0) ? $amount - $discount : 0;
    }

    protected function referOtherPercentage($referId, $productId)
    {
        $categoryId = Product::find($productId)->category_id;
        return (CustomPercent::where('refer_id', $referId)->where('category_id', $categoryId)->first())->percentage;

    }

    public function refferAmount($refferId, $totalAmount, $discount)
    {
        $ref = Reefer::find($refferId);
//        dd(round((($ref->percent*$totalAmount)/100)-$discount));
        return round((($ref->percent * $totalAmount) / 100) - $discount) > 0 ? round((($ref->percent * $totalAmount) / 100) - $discount) : 0;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->checkOwnPermission('invoices.index');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['singleData'] = Invoice::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $data['reports'] = TestReport::where('invoice_id', $id)->get();
            return view('backend.pages.invoices.show', $data);
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
        $this->checkOwnPermission('invoices.edit');
        $data['pageHeader'] = $this->pageHeader;
        if (!Invoice::withSum('paidAmount', 'paid_amount')->where('admin_id', auth()->id())->where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            if (!auth()->user()->hasRole('Owner')) {
                return RedirectHelper::routeError('admin.invoices.index', "<strong>Sorry!!! </strong> This is not Your Invoice !");
            }

        }
        if ($data['edited'] = Invoice::withSum('paidAmount', 'paid_amount')->where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            $data['products'] = InvoiceList::where('branch_id', auth()->user()->branch_id)
                ->where('invoice_id', $id)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_id' => $product->product_id,
                        'product_name' => $product->product->name, // Assuming 'product_name' is a column
                        'price' => $product->price,
                        'reefer_fee' => $product->refer_fee,
                    ];
                });
            $data['reports'] = TestReport::where('invoice_id', $id)->get();
            return view('backend.pages.invoices.edit', $data);
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
        $this->checkOwnPermission('invoices.edit');

        try {
            $rules = [
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer',
                'products.*.price' => 'required|numeric',
//                'products.*.ref_amount' => 'required|numeric',
            ];
            $request->validate($rules);
            \DB::transaction(function () use ($rules, $request, $id) {

                $row = Invoice::where('branch_id', auth()->user()->branch_id)
                    ->findOrFail($id);
                $row->dr_refer_id = $request['customerDetails']['dr_refer_id'];
                $row->dr_name = $request['customerDetails']['dr_refer_name'];
                $row->refer_id = $request['customerDetails']['refer_id'];
                $row->admin_id = auth()->id();
                $row->invoice_number = $request['customerDetails']['invoice_number'];
                $row->total_amount = $request['paymentDetails']['total_amount'];
                $row->discount_amount = $request['paymentDetails']['discount_amount'];
                $refer = Reefer::find($request['customerDetails']['refer_id']);
                if ($refer && $refer->customParcent->isNotEmpty()) {
                    $refFeeTotal =  $this->refferAmountNew($request['customerDetails']['refer_id'], $request['products'], $request['paymentDetails']['discount_amount']);
                }else{
                    $refFeeTotal = self::refferAmount($request['customerDetails']['refer_id'], ($request['paymentDetails']['total_amount'] + $request['paymentDetails']['discount_amount']), $request['paymentDetails']['discount_amount']);
                }
                if ($request['customerDetails']['refer_id'] != null) {
                    $row->refer_fee_total =$refFeeTotal;

                } else {
                    $row->refer_fee_total = 0.00;
                }
                $row->refer_fee_total_agent = $request->refer_fee_total_agent ?? 0.00;
//                $row->discount_percent = $request->discount_percent;
                $row->delivery_at = $request['customerDetails']['delivery_at'];
                $row->discount_by = $request['paymentDetails']['discount_by'];
                $row->payment_type = $request['paymentDetails']['payment_type'] ?? 'Cash';
                $row->patient_name = $request['customerDetails']['patient_name'];
                $row->patient_age_year = $request['customerDetails']['patient_age_year'];
                $row->patient_phone = $request['customerDetails']['patient_phone'];
                $row->patient_email = $request['customerDetails']['patient_email'] ?? null;
                $row->patient_gender = $request['customerDetails']['patient_gender'];
                $row->patient_blood_group = $request['customerDetails']['patient_blood_group'];
                $row->patient_address = $request['customerDetails']['patient_address'];
                $row->save();

                // Save products
                $totalProducts = count($request['products']);
                foreach ($request['products'] as $product) {
                    InvoiceList::updateOrCreate(
                        [
                            'branch_id' => auth()->user()->branch_id,
                            'invoice_id' => $row->id,
                            'product_id' => $product['product_id'],
                        ],
                        [
                            'price' => $product['price'],
                            'discount_price' => ($row->discount_amount / $totalProducts),
                        ]
                    );
                }

                $productIdsInRequest = collect($request['products'])->pluck('product_id')->toArray();

                InvoiceList::where('invoice_id', $row->id)
                    ->whereNotIn('product_id', $productIdsInRequest)
                    ->where('status', InvoiceList::$statusArray[0])
                    ->delete();
            });


            return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Invoice Created Successfully');


            return RedirectHelper::backWithInput();

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
        $this->checkOwnPermission('invoices.delete');
        $invoice = Invoice::where('branch_id', auth()->user()->branch_id)->find($id);
        if (!is_null($invoice)) {
            try {
                \DB::beginTransaction();
                InvoiceList::where('invoice_id', $invoice->id)->delete();
                InvoicePayment::where('invoice_id', $invoice->id)->delete();
                Cost::where('invoice_id', $invoice->id)->delete();
                $invoice->delete();
                \DB::commit();
                return response()->json(['status' => 200]);
            } catch (\Throwable $e) {
                \DB::rollBack();
                return response()->json(['status' => 422, 'error' => 'Delete failed.']);
            }
        }
        return response()->json(['status' => 404, 'error' => 'Invoice not found.']);

    }

    public function pdfPreview($id)
    {
        $data['invoice'] = Invoice::where('branch_id', auth()->user()->branch_id)
            ->find($id);
        $text = "Hello World";
        $qrCode = new QrCode($data['invoice']->id);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $data['qrcode'] = base64_encode($result->getString());
// Set Dhaka timezone
        $nowDhaka = Carbon::now('Asia/Dhaka');

        // Compare with 2 PM
        if ($nowDhaka->lt($nowDhaka->copy()->setTime(14, 0))) {
            $data['deliverytime'] = $nowDhaka->copy()->setTime(16, 0)->format('jS F Y h a');
        } else {
            $data['deliverytime'] = $nowDhaka->copy()->addDay()->setTime(10, 0)->format('jS F Y h a');
        }

//        return Pdf::loadView('backend.pages.invoices.invoice-pdf', $data)->stream($data['invoice']->patient_name.'.pdf');
        return view('backend.pages.invoices.invoice-regular', $data);
    }

    public function reportPdfPreview($id)
    {
        $data['pageHeader'] = $this->pageHeader;
        $data['invoice'] = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->with('invoice')->find($id);
        return Pdf::loadView('backend.pages.invoices.report-pdf', $data)->stream('preview.pdf');
    }

    public function reportPdfedit($id)
    {
        $data['pageHeader'] = $this->pageHeader;
        $data['invoice'] = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->with('invoice')->find($id);
        return Pdf::loadView('backend.pages.invoices.report-pdf', $data)->stream('preview.pdf');
    }

    public function reportfileDownload($id)
    {
        $invoice = InvoiceList::where('branch_id', auth()->user()->branch_id)
            ->with('invoice')->find($id);
        if (!$invoice) {
            return back()->with('error', 'Invoice not found.');
        }
        $filePath = public_path('images/' . $invoice->document);
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }
        $customFileName = $invoice->invoice->patient_name . '_Report_' . $invoice->invoice->invoice_number . '.' . pathinfo($invoice->document, PATHINFO_EXTENSION);
        return response()->download($filePath, $customFileName);
    }

    public function invoiceStatus($id)
    {
//        $this->checkOwnPermission('invoices.edit');
        $row = Invoice::where('branch_id', auth()->user()->branch_id)
            ->find($id);
        if (Invoice::$deliveryStatusArray[0] == $row->status) {
            $row->status = Invoice::$deliveryStatusArray[1];
        }
        if (($row->save())) {
            return response()->json(['status' => 200]);
        } else {
            return response()->json(['status' => 422]);

        }
    }


    public function invoiceDuePay(Request $request, $id)
    {
        $rules = [
            'due_pay' => 'required|numeric',
        ];
        $request->validate($rules);
        if ($request->due_pay != 0) {
            $duePaid = new InvoicePayment();
            $duePaid->branch_id = auth()->user()->branch_id;
            $duePaid->admin_id = auth()->id();
            $duePaid->paid_amount = $request->due_pay;
            $duePaid->invoice_id = $id;
            $duePaid->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            if ($duePaid->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong>Due paid');
            } else {
                return RedirectHelper::backWithInputFromException();

            }
        } else {
            RedirectHelper::backWithInputFromException();
        }
    }

    public function getNextPatientNo()
    {
        $latestPatientNo = Invoice::where('branch_id', auth()->user()->branch_id)
            ->get()
            ->max(fn($row) => (int)$row->patient_no);
        return $latestPatientNo + 1;

    }


}

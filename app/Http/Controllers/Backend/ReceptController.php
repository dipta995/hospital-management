<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Admit;
use App\Models\Recept;
use App\Models\ReceptList;
use App\Models\ReceptPayment;
use App\Models\CustomerBalance;
use App\Models\ServiceCategory;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReceptController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.recepts.index";
    public $create_route = "admin.recepts.create";
    public $store_route = "admin.recepts.store";
    public $edit_route = "admin.recepts.edit";
    public $update_route = "admin.recepts.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Recepts",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/recepts'),
        ];
    }

    public function index(Request $request)
    {
        $this->checkOwnPermission('recepts.index');
        $data['pageHeader'] = $this->pageHeader;
        $for = $request->get('for'); // example: ?for=5 (admit id)

        $query = Recept::where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC');

        // Filter by admit if provided
        if (!empty($for)) {
            $query->where('admit_id', $for);
        }

        // Date filtering
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate || $endDate) {
            if ($startDate) {
                $query->whereDate('created_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_date', '<=', $endDate);
            }
        } else {
            // Default to current month
            $query->whereYear('created_date', now('Asia/Dhaka')->year)
                  ->whereMonth('created_date', now('Asia/Dhaka')->month);
        }

        // Clone query for summary totals
        $summaryQuery = clone $query;
        $receiptsForSummary = $summaryQuery->with('receptPayments')->get();

        $totalAmount = $receiptsForSummary->sum('total_amount');
        $totalDiscount = $receiptsForSummary->sum('discount_amount');
        $totalPaid = $receiptsForSummary->sum(function ($r) {
            return $r->receptPayments->sum('paid_amount');
        });

        $data['total_amount'] = $totalAmount;
        $data['total_discount'] = $totalDiscount;
        $data['total_paid'] = $totalPaid;

        $data['datas'] = $query->with(['user', 'receptPayments'])->paginate(20)->appends($request->all());

        return view('backend.pages.recepts.index', $data);
    }

    public function create()
    {
        $this->checkOwnPermission('recepts.create');
        $data['pageHeader'] = $this->pageHeader;
        $admitId = request('admitId');

        if ($admitId) {
            $admit = Admit::where('branch_id', auth()->user()->branch_id)->find($admitId);

            if (!$admit) {
                return RedirectHelper::routeError('admin.admits.index', 'Admit not found.');
            }

            if ($admit->release_at) {
                return RedirectHelper::routeError('admin.admits.index', 'Cannot create receipt for a released admit.');
            }
        }

        $data['user_data'] = User::find(request('for'));
        $data['service_categories'] = ServiceCategory::all();
        return view('backend.pages.recepts.create', $data);
    }

    public function store(Request $request)
    {
//        return $request;
        $this->checkOwnPermission('recepts.create');

        DB::beginTransaction(); // 🔹 Start transaction

        try {
            // If linked to an admit, ensure it is not released
            $admitId = $request['customerDetails']['admit_id'] ?? null;
            if ($admitId) {
                $admit = Admit::where('branch_id', auth()->user()->branch_id)->find($admitId);
                if (!$admit || $admit->release_at) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Cannot create receipt for a released or invalid admit.',
                    ], 422);
                }
            }

            // 1️⃣ Create Recept
            $row = new Recept();
            $row->admit_id = $request['customerDetails']['admit_id'];
            $row->admin_id = auth()->id();
            $row->user_id = $request['customerDetails']['customer_id'];
            $row->branch_id = auth()->user()->branch_id;
            $row->total_amount = $request['paymentDetails']['total_amount'];
            $row->discount_amount = $request['paymentDetails']['discount_amount'];
            $row->created_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            $row->discount_amount = $request['paymentDetails']['discount_amount'] ?? 0;
            $row->save();

            // 2️⃣ Create ReceptList entries
            foreach ($request['services'] as $service) {
                ReceptList::create([
                    'branch_id'      => auth()->user()->branch_id,
                    'user_id'        => $request['customerDetails']['customer_id'],
                    'recept_id'      => $row->id,
                    'service_id'     => $service['service_id'],
                    'price'          => $service['price'],
                    'discount'       => 0,
                ]);
            }

            // 3️⃣ Create Payment entry
            $duePaid = new ReceptPayment();
            $duePaid->paid_amount = $request['paymentDetails']['paid_amount'];
            $duePaid->recept_id = $row->id;
            $duePaid->branch_id = auth()->user()->branch_id;
            $duePaid->admin_id = auth()->id();
            $duePaid->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            $duePaid->save();

            // Optionally record advance balance if provided (customer balance top-up)
            $advanceBalance = $request->input('paymentDetails.advance_balance');
            if (!empty($advanceBalance) && $advanceBalance > 0) {
                $balance = CustomerBalance::firstOrNew([
                    'user_id' => $row->user_id,
                    'branch_id' => $row->branch_id,
                ]);
                $balance->balance = ($balance->balance ?? 0) + $advanceBalance;
                $balance->save();
            }

            // 🔹 Commit all only if every step was successful
            DB::commit();

            return response()->json(
                [
                    'recept_id' => $row->id,
                    'customer_name' => $row->user->name,
                ]
            );
        } catch (QueryException $e) {
            DB::rollBack(); // 🔹 Cancel everything on error
            return RedirectHelper::backWithInputFromException($e);
        } catch (\Exception $e) {
            DB::rollBack();
            return RedirectHelper::backWithInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->checkOwnPermission('recepts.edit');
        $data['pageHeader'] = $this->pageHeader;

        $recept = Recept::where('branch_id', auth()->user()->branch_id)
            ->with(['user', 'receptList.service', 'receptPayments'])
            ->find($id);

        if (!$recept) {
            return RedirectHelper::routeError($this->index_route, 'Recept not found.');
        }

        $data['edited'] = $recept;
        $data['service_categories'] = ServiceCategory::all();
        $data['user_data'] = $recept->user; // for showing patient info similar to create

        return view('backend.pages.recepts.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('recepts.edit');

        DB::beginTransaction();

        try {
            $recept = Recept::where('branch_id', auth()->user()->branch_id)->findOrFail($id);

            $services = $request->input('services', []);
            $customerDetails = $request->input('customerDetails', []);
            $paymentDetails = $request->input('paymentDetails', []);

            // Basic validation
            if (empty($services)) {
                return response()->json(['status' => 422, 'message' => 'No services provided.']);
            }

            // Update main recept
            $recept->admit_id = $customerDetails['admit_id'] ?? $recept->admit_id;
            $recept->user_id = $customerDetails['customer_id'] ?? $recept->user_id;
            $recept->branch_id = auth()->user()->branch_id;
            $recept->total_amount = $paymentDetails['total_amount'] ?? $recept->total_amount;
            $recept->discount_amount = $paymentDetails['discount_amount'] ?? 0;
            // keep existing created_date
            $recept->save();

            // Rebuild recept list
            ReceptList::where('recept_id', $recept->id)->delete();

            foreach ($services as $service) {
                ReceptList::create([
                    'branch_id'      => auth()->user()->branch_id,
                    'user_id'        => $recept->user_id,
                    'recept_id'      => $recept->id,
                    'service_id'     => $service['service_id'],
                    'price'          => $service['price'],
                    'discount'       => 0,
                ]);
            }

            // Rebuild payments (single payment record like create)
            ReceptPayment::where('recept_id', $recept->id)->delete();

            $duePaid = new ReceptPayment();
            $duePaid->paid_amount = $paymentDetails['paid_amount'] ?? 0;
            $duePaid->recept_id = $recept->id;
            $duePaid->branch_id = auth()->user()->branch_id;
            $duePaid->admin_id = auth()->id();
            $duePaid->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            $duePaid->save();

            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Recept updated successfully.']);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function pay(Request $request, $id)
    {
        $this->checkOwnPermission('recepts.edit');

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'pay_from_balance' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $recept = Recept::where('branch_id', auth()->user()->branch_id)->findOrFail($id);

            $total = $recept->total_amount ?? 0;
            $discount = $recept->discount_amount ?? 0;
            $paid = $recept->receptPayments->sum('paid_amount');
            $net = $total - $discount;
            $due = $net - $paid;

            $amount = min($request->amount, $due);

            if ($amount <= 0) {
                return response()->json(['status' => 422, 'message' => 'Nothing due to pay.']);
            }

            $fromBalance = $request->boolean('pay_from_balance');

            if ($fromBalance) {
                $balance = CustomerBalance::firstOrNew([
                    'user_id' => $recept->user_id,
                    'branch_id' => $recept->branch_id,
                ]);

                if (($balance->balance ?? 0) < $amount) {
                    return response()->json(['status' => 422, 'message' => 'Insufficient balance.']);
                }

                $balance->balance -= $amount;
                $balance->save();
            }

            $payment = new ReceptPayment();
            $payment->recept_id = $recept->id;
            $payment->branch_id = $recept->branch_id;
            $payment->admin_id = auth()->id();
            $payment->paid_amount = $amount;
            $payment->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            $payment->save();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Payment successfully recorded.',
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('recepts.delete');
        $deleteData = Recept::where('branch_id', auth()->user()->branch_id)->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }

    public function receptPdfPreview($id)
    {
        $recept = Recept::with(['receptList.service', 'user', 'admin', 'receptPayments'])
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!$recept) {
            return RedirectHelper::routeError($this->index_route, 'Recept not found.');
        }

        $data['recept'] = $recept;

        $qrCode = new QrCode($recept->id);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $data['qrcode'] = base64_encode($result->getString());
// Set Dhaka timezone
        $nowDhaka = \Carbon\Carbon::now('Asia/Dhaka');

        // Compare with 2 PM
        if ($nowDhaka->lt($nowDhaka->copy()->setTime(14, 0))) {
            $data['deliverytime'] = $nowDhaka->copy()->setTime(16, 0)->format('jS F Y h a');
        } else {
            $data['deliverytime'] = $nowDhaka->copy()->addDay()->setTime(10, 0)->format('jS F Y h a');
        }

//        return Pdf::loadView('backend.pages.recepts.recept-pdf', $data)->stream($recept->user->name . '.pdf');
    return view('backend.pages.recepts.recept-regular', $data);
    }
}

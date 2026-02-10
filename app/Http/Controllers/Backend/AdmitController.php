<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Admit;
use App\Models\BedCabin;
use App\Models\Cost;
use App\Models\CostCategory;
use App\Models\Recept;
use App\Models\ReceptPayment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class AdmitController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.admits.index";
    public $create_route = "admin.admits.create";
    public $store_route = "admin.admits.store";
    public $edit_route = "admin.admits.edit";
    public $update_route = "admin.admits.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "admits",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/admits'),
        ];
    }

    public function index(Request $request)
    {
        $this->checkOwnPermission('admits.index');

        $data['pageHeader'] = $this->pageHeader;
        $query = Admit::with(['reefer', 'drreefer', 'user'])
            ->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $data['datas'] = $query->paginate(10)->appends($request->all());

        $data['users'] = User::all();

        return view('backend.pages.admits.index', $data);
    }


    public function create()
    {
        $this->checkOwnPermission('admits.create');
        $data['pageHeader'] = $this->pageHeader;
        $data['user'] = User::find(request('for'));

        if (!$data['user']) {
            return RedirectHelper::routeError($this->index_route, 'User not found.');
        }

        // Beds/cabins that are not currently assigned to an active admit (release_at is null)
        $occupiedBedIds = Admit::where('branch_id', auth()->user()->branch_id)
            ->whereNull('release_at')
            ->whereNotNull('bed_cabin_id')
            ->pluck('bed_cabin_id');

        $data['beds'] = BedCabin::where('branch_id', auth()->user()->branch_id)
            ->when($occupiedBedIds->isNotEmpty(), function ($q) use ($occupiedBedIds) {
                $q->whereNotIn('id', $occupiedBedIds);
            })
            ->orderBy('name')
            ->get();

        return view('backend.pages.admits.create', $data);
    }


    public function store(Request $request)
    {
        $this->checkOwnPermission('admits.create');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'admit_at' => 'required|string',
            'bed_cabin_id' => 'nullable|exists:bed_cabins,id',
        ]);

        try {
            $row = new Admit();
                $row->user_id = $request->user_id;
            $row->branch_id = auth()->user()->branch_id;
            $row->refer_id = $request->refer_id;
            $row->dr_refer_id = $request->dr_refer_id;
            $row->admit_at = $request->admit_at ? Carbon::parse($request->admit_at)->format('Y-m-d H:i:s') : null;
            $row->release_at = $request->release_at ? Carbon::parse($request->release_at)->format('Y-m-d H:i:s') : null;
            $row->nid = $request->nid;
            $row->note = $request->note;

            // Link to selected bed/cabin and store its name for reference
            if ($request->bed_cabin_id) {
                $bed = BedCabin::where('branch_id', auth()->user()->branch_id)
                    ->find($request->bed_cabin_id);
                if ($bed) {
                    $row->bed_cabin_id = $bed->id;
                    $row->bed_or_cabin = $bed->name;
                }
            } else {
                $row->bed_or_cabin = $request->bed_or_cabin;
            }
            $row->father_or_spouse = $request->father_or_spouse;
            $row->received_by = $request->received_by;
            $row->clinical_diagnosis = $request->clinical_diagnosis;
            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, 'Admit created successfully.');
            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException($e);
        }
    }


    public function edit($id)
    {
        $this->checkOwnPermission('admits.edit');
        $data['pageHeader'] = $this->pageHeader;

        $data['edited'] = Admit::with('reefer')
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        if ($data['edited']->release_at) {
            return RedirectHelper::routeError($this->index_route, 'Released admits cannot be edited.');
        }

        $data['users'] = User::all();

        return view('backend.pages.admits.edit', $data);
    }


    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('admits.edit');
        $request->validate([
//            'name' => 'required|max:200',
//            'price' => 'required|numeric',
        ]);

        try {
            if ($row = Admit::where('branch_id', auth()->user()->branch_id)->find($id)) {
                if ($row->release_at) {
                    return RedirectHelper::routeError($this->index_route, 'Released admits cannot be updated.');
                }
                $row->admit_at = $request->admit_at ? Carbon::parse($request->admit_at)->format('Y-m-d H:i:s') : null;
                $row->release_at = $request->release_at ? Carbon::parse($request->release_at)->format('Y-m-d H:i:s') : null;
                $row->nid = $request->nid;
                $row->note = $request->note;


                $row->bed_or_cabin = $request->bed_or_cabin;
                $row->father_or_spouse = $request->father_or_spouse;
                $row->received_by = $request->received_by;
                $row->clinical_diagnosis = $request->clinical_diagnosis;
                $row->refer_id = $request->refer_id;
                $row->dr_refer_id = $request->dr_refer_id;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, 'Admit updated successfully.');
                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, 'Admit not found.');
            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException($e);
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('admits.delete');
        $admit = Admit::with('recepts')
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!$admit) {
            return response()->json(['status' => 404]);
        }

        if ($admit->release_at) {
            // Do not allow deleting released admits
            return response()->json(['status' => 422]);
        }

        try {
            \DB::beginTransaction();

            // Delete all related recept data (lists + payments + recepts)
            $receptIds = $admit->recepts->pluck('id');

            if ($receptIds->isNotEmpty()) {
                \App\Models\ReceptList::whereIn('recept_id', $receptIds)->delete();
                \App\Models\ReceptPayment::whereIn('recept_id', $receptIds)->delete();
                \App\Models\Recept::whereIn('id', $receptIds)->delete();
            }

            // Finally delete the admit
            $admit->delete();

            \DB::commit();
            return response()->json(['status' => 200]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['status' => 422]);
        }
    }

    public function storeRelease(Request $request, $id)
    {
        $this->checkOwnPermission('admits.edit');

        $request->validate([
            'release_at' => 'required|string',
        ]);

        $admit = Admit::where('branch_id', auth()->user()->branch_id)->findOrFail($id);

        if ($admit->release_at) {
            return RedirectHelper::routeError($this->index_route, 'Admit already released.');
        }

        $admit->release_at = Carbon::parse($request->release_at)->format('Y-m-d H:i:s');
        $admit->save();

        return RedirectHelper::routeSuccess($this->index_route, 'Admit released successfully.');
    }


    public function releaseDetails($id)
    {
        $this->checkOwnPermission('admits.index');

        $admit = Admit::with(['user', 'reefer', 'drreefer', 'bedCabin', 'recepts.receptPayments'])
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        $receipts = $admit->recepts;

        $totalAmount = $receipts->sum('total_amount');
        $totalDiscount = $receipts->sum('discount_amount');
        $netTotal = $totalAmount - $totalDiscount;
        $totalPaid = $receipts->sum(function ($r) {
            return $r->receptPayments->sum('paid_amount');
        });
        $totalDue = max($netTotal - $totalPaid, 0);

        // Existing PC payment for this admit (if any)
        $pcPayment = null;
        $admitReferCategoryId = Setting::get('admit_refer_cost_category');
        if ($admitReferCategoryId) {
            $pcPayment = Cost::where('branch_id', auth()->user()->branch_id)
                ->where('cost_category_id', $admitReferCategoryId)
                ->where('account_details', 'admit_id:' . $admit->id)
                ->first();
        }

        return view('backend.pages.admits.release', [
            'pageHeader'    => $this->pageHeader,
            'admit'         => $admit,
            'receipts'      => $receipts,
            'total_amount'  => $totalAmount,
            'total_discount'=> $totalDiscount,
            'net_total'     => $netTotal,
            'total_paid'    => $totalPaid,
            'total_due'     => $totalDue,
            'pcPayment'     => $pcPayment,
        ]);
    }


    public function releasePrint($id)
    {
        $this->checkOwnPermission('admits.index');

        $admit = Admit::with(['user', 'reefer', 'drreefer', 'bedCabin', 'recepts.receptPayments'])
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        $receipts = $admit->recepts;

        $totalAmount = $receipts->sum('total_amount');
        $totalDiscount = $receipts->sum('discount_amount');
        $netTotal = $totalAmount - $totalDiscount;
        $totalPaid = $receipts->sum(function ($r) {
            return $r->receptPayments->sum('paid_amount');
        });
        $totalDue = max($netTotal - $totalPaid, 0);

        return view('backend.pages.admits.release-print', [
            'admit'         => $admit,
            'receipts'      => $receipts,
            'total_amount'  => $totalAmount,
            'total_discount'=> $totalDiscount,
            'net_total'     => $netTotal,
            'total_paid'    => $totalPaid,
            'total_due'     => $totalDue,
        ]);
    }


    public function payDue(Request $request, $id)
    {
        $this->checkOwnPermission('admits.edit');

        $admit = Admit::with('recepts.receptPayments')
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        if ($admit->release_at) {
            return RedirectHelper::routeError($this->index_route, 'Cannot pay due for a released admit.');
        }

        $receipts = $admit->recepts;

        $totalAmount = $receipts->sum('total_amount');
        $totalDiscount = $receipts->sum('discount_amount');
        $netTotal = $totalAmount - $totalDiscount;
        $totalPaid = $receipts->sum(function ($r) {
            return $r->receptPayments->sum('paid_amount');
        });
        $totalDue = max($netTotal - $totalPaid, 0);

        if ($totalDue <= 0) {
            return RedirectHelper::routeError($this->index_route, 'No due amount to pay for this admit.');
        }

        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
        ]);

        $remaining = min($request->paid_amount, $totalDue);

        foreach ($receipts->sortBy('id') as $recept) {
            $net = $recept->total_amount - $recept->discount_amount;
            $paid = $recept->receptPayments->sum('paid_amount');
            $due = $net - $paid;

            if ($due <= 0) {
                continue;
            }

            $payNow = min($remaining, $due);

            if ($payNow > 0) {
                ReceptPayment::create([
                    'recept_id'     => $recept->id,
                    'branch_id'     => auth()->user()->branch_id,
                    'admin_id'      => auth()->id(),
                    'paid_amount'   => $payNow,
                    'creation_date' => Carbon::now('Asia/Dhaka')->format('Y-m-d'),
                ]);

                $remaining -= $payNow;

                if ($remaining <= 0) {
                    break;
                }
            }
        }

        // Flash a plain message for JS toast
        session()->flash('toast_message', 'Due paid successfully.');

        return RedirectHelper::routeSuccessWithSubParam('admin.admits.release.details', $admit->id, 'Due paid successfully.');
    }


    public function pcPayment(Request $request, $id)
    {
        $this->checkOwnPermission('admits.edit');

        $admit = Admit::with('reefer')
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        $admitReferCategoryId = Setting::get('admit_refer_cost_category');

        if (!$admitReferCategoryId) {
            return RedirectHelper::routeError($this->index_route, 'Admit refer cost category is not configured.');
        }

        try {
            // Find existing PC payment for this admit, or create new
            $cost = Cost::where('branch_id', auth()->user()->branch_id)
                ->where('cost_category_id', $admitReferCategoryId)
                ->where('account_details', 'admit_id:' . $admit->id)
                ->first();

            // If cost exists but its creation_date is not today, block update
            if ($cost && $cost->creation_date && $cost->creation_date !== Carbon::now('Asia/Dhaka')->format('Y-m-d')) {
                return RedirectHelper::routeError($this->index_route, 'PC payment can only be updated on the current date.');
            }

            if (!$cost) {
                $cost = new Cost();
                $cost->branch_id = auth()->user()->branch_id;
                $cost->cost_category_id = $admitReferCategoryId;
                $cost->refer_id = $admit->refer_id;
                $cost->account_details = 'admit_id:' . $admit->id;
                $cost->payment_type = Cost::$paymentArray[0];
                $cost->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
            }

            $cost->reason = $request->reason ?: ('PC Payment for Admit #' . $admit->id);
            $cost->amount = $request->amount;
            $cost->save();

            if (Setting::get('pc_payment_sms') == 'Yes' && $admit->reefer && $admit->reefer->phone) {
                $format = Setting::get('refer_payment_sms_format');
                $message = str_replace(
                    ['{amount}'],
                    [$cost->amount],
                    $format
                );

                smsSent(auth()->user()->branch_id, $admit->reefer->phone, $message);
            }

            return RedirectHelper::routeSuccessWithSubParam('admin.admits.release.details', $admit->id, 'PC payment saved successfully.');
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException($e);
        }
    }


    public function print($id)
    {
        $this->checkOwnPermission('admits.index');

        $admit = Admit::with(['user', 'reefer', 'drreefer', 'bedCabin'])
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!$admit) {
            return RedirectHelper::routeError($this->index_route, 'Admit not found.');
        }

        return view('backend.pages.admits.print', [
            'admit' => $admit,
        ]);
    }


}

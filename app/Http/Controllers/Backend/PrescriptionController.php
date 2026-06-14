<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\Prescription;
use App\Models\Drug;
use App\Models\Product;
use App\Models\Reefer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;

class PrescriptionController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.prescriptions.index";
    public $create_route = "admin.prescriptions.create";
    public $store_route = "admin.prescriptions.store";
    public $edit_route = "admin.prescriptions.edit";
    public $update_route = "admin.prescriptions.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Prescriptions",
            'sub_title' => "",
            'plural_name' => "prescriptions",
            'singular_name' => "Prescription",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'show_route' => 'admin.prescriptions.show',
            'delete_route' => 'admin.prescriptions.destroy',
            'base_url' => url('admin/prescriptions'),
        ];
    }

    private function linkedReefer(): ?Reefer
    {
        return Reefer::where('branch_id', auth()->user()->branch_id)
            ->where('admin_id', auth()->id())
            ->first();
    }

    public function index(Request $request)
    {
        $this->checkOwnPermission('prescriptions.index');

        $branchId = auth()->user()->branch_id;
        $linkedDoctor = $this->linkedReefer();

        $query = Prescription::with(['doctor', 'invoice'])
            ->where('branch_id', $branchId)
            ->latest();

        if ($linkedDoctor) {
            $query->where('reefer_id', $linkedDoctor->id);
        } elseif ($request->filled('doctor_id')) {
            $query->where('reefer_id', $request->doctor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('diagnosis', 'like', "%{$search}%")
                    ->orWhere('investigation', 'like', "%{$search}%")
                    ->orWhereHas('invoice', fn ($inv) => $inv->where('patient_name', 'like', "%{$search}%"))
                    ->orWhereHas('doctor', fn ($doc) => $doc->where('name', 'like', "%{$search}%"));
            });
        }

        $prescriptions = $query->paginate(15)->withQueryString();

        $doctors = Reefer::where('branch_id', $branchId)
            ->where('type', Reefer::$typeArray[0])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('backend.pages.prescriptions.index', [
            'prescriptions' => $prescriptions,
            'pageHeader' => $this->pageHeader,
            'doctors' => $doctors,
            'linkedDoctor' => $linkedDoctor,
        ]);
    }

    public function create()
    {
        $this->checkOwnPermission('prescriptions.create');

        $linkedDoctor = $this->linkedReefer();
        $branchId = auth()->user()->branch_id;

        return view('backend.pages.prescriptions.create', [
            'pageHeader' => $this->pageHeader,
            'doctorId' => $linkedDoctor?->id,
            'doctors' => Reefer::where('branch_id', $branchId)
                ->where('type', Reefer::$typeArray[0])
                ->orderBy('name')
                ->get(['id', 'name']),
            'products' => Product::where('branch_id', $branchId)
                ->orderBy('name')
                ->get(['name', 'price', 'id']),
        ]);
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('prescriptions.create');

        $linkedDoctor = $this->linkedReefer();
        $doctorId = $request->input('doctor') ?: $linkedDoctor?->id;

        if (!$doctorId) {
            return RedirectHelper::backWithInputFromException('<strong>Error!</strong> Please select a doctor or link your account to a doctor profile.');
        }

        $request->validate([
            'doctor' => 'nullable|exists:reefers,id',
            'patient_name' => 'required|string|max:200',
            'patient_age_year' => 'required',
            'patient_gender' => 'required|string|max:20',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'discount' => 'nullable|numeric|min:0|max:30',
            'investigation' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'note' => 'nullable|string|max:190',
        ]);

        $doctorId = (int) $doctorId;
        $totalAmount = 0;
        $products = Product::where('branch_id', auth()->user()->branch_id)
            ->whereIn('id', $request->product_ids)
            ->get()
            ->keyBy('id');

        foreach ($request->product_ids as $productId) {
            if (!$products->has($productId)) {
                return RedirectHelper::backWithInputFromException('<strong>Error!</strong> Invalid test selected.');
            }
            $totalAmount += (float) $products[$productId]->price;
        }

        $discountPercent = (float) ($request->discount ?? 0);
        $discountOnlyTotal = ($totalAmount * $discountPercent) / 100;
        $discountedAmount = $totalAmount - $discountOnlyTotal;

        $row = new Invoice();
        $row->branch_id = auth()->user()->branch_id;
        $row->patient_no = self::getNextPatientNo();
        $row->dr_refer_id = $doctorId;
        $row->refer_id = $doctorId;
        $row->admin_id = auth()->id();
        $row->invoice_number = self::generateInvoiceNumber();
        $row->discount_by = auth()->user()->name;
        $row->total_amount = $discountedAmount;
        $row->discount_amount = $discountOnlyTotal;
        $row->refer_fee_total = self::refferAmount($doctorId, ($discountedAmount + $discountOnlyTotal), $discountOnlyTotal);
        $row->refer_fee_total_agent = 0.00;
        $row->delivery_at = Carbon::now('Asia/Dhaka');
        $row->payment_type = 'Cash';
        $row->patient_name = $request->patient_name;
        $row->patient_age_year = $request->patient_age_year;
        $row->patient_gender = $request->patient_gender;
        $row->patient_blood_group = $request->patient_blood_group;
        $row->note = $request->note;
        $row->creation_date = Carbon::now('Asia/Dhaka')->format('Y-m-d');
        $row->save();

        foreach ($request->product_ids as $productId) {
            $test = $products[$productId];
            InvoiceList::create([
                'branch_id' => auth()->user()->branch_id,
                'invoice_id' => $row->id,
                'product_id' => $test->id,
                'price' => $test->price,
                'refer_fee' => $test->refer_fee,
            ]);
        }

        $prescription = Prescription::create([
            'branch_id' => auth()->user()->branch_id,
            'invoice_id' => $row->id,
            'reefer_id' => $doctorId,
            'investigation' => $request->investigation,
            'diagnosis' => $request->diagnosis,
        ]);

        if ($request->filled('drug_name')) {
            foreach ($request->drug_name as $index => $name) {
                if (trim((string) $name) === '') {
                    continue;
                }
                Drug::create([
                    'prescription_id' => $prescription->id,
                    'name' => $name,
                    'rule' => $request->drug_rule[$index] ?? null,
                    'time' => $request->drug_time[$index] ?? null,
                    'note' => $request->drug_note[$index] ?? null,
                    'duration' => $request->drug_duration[$index] ?? null,
                ]);
            }
        }

        return RedirectHelper::routeSuccess($this->index_route, '<strong>Success!</strong> Prescription created successfully!');
    }

    public function show($id)
    {
        $this->checkOwnPermission('prescriptions.index');

        $prescription = Prescription::with('drugs', 'doctor', 'invoice')
            ->where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!$prescription) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!</strong> No prescription found.');
        }

        $linkedDoctor = $this->linkedReefer();
        if ($linkedDoctor && (int) $prescription->reefer_id !== (int) $linkedDoctor->id) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!</strong> You cannot view this prescription.');
        }

        $tests = InvoiceList::with('product')
            ->where('invoice_id', $prescription->invoice_id)
            ->get();

        return view('backend.pages.prescriptions.show', [
            'pageHeader' => $this->pageHeader,
            'prescription' => $prescription,
            'tests' => $tests,
        ]);
    }

    public function edit($id)
    {
        $this->checkOwnPermission('prescriptions.edit');

        $prescription = Prescription::with('drugs', 'doctor', 'invoice')
            ->where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        return view('backend.pages.prescriptions.edit', [
            'prescription' => $prescription,
            'reefers' => Reefer::where('branch_id', auth()->user()->branch_id)
                ->where('type', Reefer::$typeArray[0])
                ->orderBy('name')
                ->get(['id', 'name']),
            'pageHeader' => $this->pageHeader,
            'linkedDoctor' => $this->linkedReefer(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('prescriptions.edit');

        $request->validate([
            'reefer_id' => 'required|exists:reefers,id',
            'investigation' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'drug_name' => 'nullable|array',
            'drug_name.*' => 'nullable|string|max:255',
        ]);

        try {
            $prescription = Prescription::where('branch_id', auth()->user()->branch_id)
                ->findOrFail($id);

            $prescription->reefer_id = $request->reefer_id;
            $prescription->investigation = $request->investigation;
            $prescription->diagnosis = $request->diagnosis;
            $prescription->save();

            Drug::where('prescription_id', $id)->delete();

            if ($request->filled('drug_name')) {
                foreach ($request->drug_name as $index => $name) {
                    if (trim((string) $name) === '') {
                        continue;
                    }
                    Drug::create([
                        'prescription_id' => $id,
                        'name' => $name,
                        'rule' => $request->drug_rule[$index] ?? null,
                        'time' => $request->drug_time[$index] ?? null,
                        'note' => $request->drug_note[$index] ?? null,
                        'duration' => $request->drug_duration[$index] ?? null,
                    ]);
                }
            }

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Success!</strong> Prescription updated successfully!');
        } catch (QueryException $e) {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function destroy($id)
    {
        $this->checkOwnPermission('prescriptions.delete');

        try {
            $prescription = Prescription::where('branch_id', auth()->user()->branch_id)
                ->findOrFail($id);
            Drug::where('prescription_id', $prescription->id)->delete();
            $prescription->delete();

            return RedirectHelper::routeSuccess($this->index_route, '<strong>Success!</strong> Prescription deleted successfully!');
        } catch (\Exception $e) {
            return RedirectHelper::backWithInputFromException();
        }
    }

    public function generateInvoiceNumber()
    {
        $latestInvoice = Invoice::where('branch_id', auth()->user()->branch_id)
            ->whereYear('creation_date', Carbon::now('Asia/Dhaka')->year)
            ->whereMonth('creation_date', Carbon::now('Asia/Dhaka')->month)
            ->latest('invoice_number')
            ->first();

        if ($latestInvoice) {
            $lastNumber = (int) substr($latestInvoice->invoice_number, 4);
            return 'INV-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return 'INV-0001';
    }

    public function refferAmount($refferId, $totalAmount, $discount)
    {
        $ref = Reefer::find($refferId);
        if (!$ref) {
            return 0;
        }

        return round((($ref->percent * $totalAmount) / 100) - $discount) > 0
            ? round((($ref->percent * $totalAmount) / 100) - $discount)
            : 0;
    }

    public function getNextPatientNo()
    {
        $latestPatientNo = Invoice::where('branch_id', auth()->user()->branch_id)->max('patient_no');
        return $latestPatientNo ? $latestPatientNo + 1 : 1;
    }
}

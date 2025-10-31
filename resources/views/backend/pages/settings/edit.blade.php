@extends('backend.layouts.master')
@section('title')
    Manage Setting
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Modify Information</h4>
                            @include('backend.layouts.partials.message')

                            <form method="post"
                                  action="{{ route($pageHeader['update_route']) }}" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <fieldset>
                                    <div class="form-group">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" name="company_name" id="company_name"
                                               value="{{ $edited['company_name'] ?? '' }}" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="logo">Logo</label>
                                        <input type="file" name="logo" id="logo" class="form-control">
                                        @if(isset($edited['logo']))
                                            <img src="{{ asset('images/' . $edited['logo']) }}" alt="Logo"
                                                 style="max-height: 100px;">
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" value="{{ $edited['email'] ?? '' }}"
                                               class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_one">Phone One</label>
                                        <input type="text" name="phone_one" id="phone_one"
                                               value="{{ $edited['phone_one'] ?? '' }}" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_two">Phone Two</label>
                                        <input type="text" name="phone_two" id="phone_two"
                                               value="{{ $edited['phone_two'] ?? '' }}"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" id="address"
                                                  class="form-control">{{ $edited['address'] ?? '' }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="purchase_category">Purchase Category</label>
                                        <select name="purchase_category" id="purchase_category" class="form-control">
                                            <option value="">Choose</option>
                                            @foreach(\App\Models\CostCategory::where('branch_id',auth()->user()->branch_id)->get() as $item)
                                                <option
                                                    value="{{ $item->id }}" @selected(old('purchase_category', $edited['purchase_category'] ?? '') == $item->id)>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pc_payment_sms">PC Payment SMS</label>
                                        <select name="pc_payment_sms" id="pc_payment_sms" class="form-control">
                                            <option value="">Choose</option>
                                            <option
                                                value="Yes" @selected(old('pc_payment_sms', $edited['pc_payment_sms'] ?? '') == 'Yes')>
                                                Yes
                                            </option>
                                            <option
                                                value="No" @selected(old('pc_payment_sms', $edited['pc_payment_sms'] ?? '') == 'No')>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="doctors_appointment">Doctor's Serial to get appointment </label>
                                        <select name="doctors_appointment" id="doctors_appointment"
                                                class="form-control">
                                            <option value="">Choose</option>
                                            <option
                                                value="Yes" @selected(old('doctors_appointment', $edited['doctors_appointment'] ?? '') == 'Yes')>
                                                Yes
                                            </option>
                                            <option
                                                value="No" @selected(old('doctors_appointment', $edited['doctors_appointment'] ?? '') == 'No')>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="doctors_appointment_sms_format">
                                            Doctor's Serial to get appointment TEXT
                                            <br>
                                            {patient_name}', '{serial}', '{date}', '{time}','{doctor}
                                        </label>
                                        <textarea name="doctors_appointment_sms_format"
                                                  id="doctors_appointment_sms_format" rows="7"
                                                  class="form-control">{{ $edited['doctors_appointment_sms_format'] ?? '' }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="refer_payment_sms_format">
                                            Refers's Payment
                                            <br>
                                            {amount}
                                        </label>
                                        <textarea name="refer_payment_sms_format"
                                                  id="refer_payment_sms_format" rows="7"
                                                  class="form-control">{{ $edited['refer_payment_sms_format'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="salary_category">Salary Category</label>
                                        <select name="salary_category" id="salary_category" class="form-control">
                                            <option value="">Choose</option>
                                            @foreach(\App\Models\CostCategory::where('branch_id',auth()->user()->branch_id)->get() as $item)
                                                <option
                                                    value="{{ $item->id }}" @selected(old('salary_category', $edited['salary_category'] ?? '') == $item->id)>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_sms">Invoice SMS</label>
                                        <select name="invoice_sms" id="invoice_sms" class="form-control">
                                            <option value="">Choose</option>
                                            <option
                                                value="Yes" @selected(old('invoice_sms', $edited['invoice_sms'] ?? '') == 'Yes')>
                                                Yes
                                            </option>
                                            <option
                                                value="No" @selected(old('invoice_sms', $edited['invoice_sms'] ?? '') == 'No')>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_customer_sms_format">
                                            Customer Sms
                                            <br>
                                            {amount}
                                        </label>
                                        <textarea name="invoice_customer_sms_format"
                                                  id="invoice_customer_sms_format" rows="7"
                                                  class="form-control">{{ $edited['invoice_customer_sms_format'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice_doctor_sms_format">
                                            Doctor / Ref sms
                                            <br>
                                            {amount}
                                        </label>
                                        <textarea name="invoice_doctor_sms_format"
                                                  id="invoice_doctor_sms_format" rows="7"
                                                  class="form-control">{{ $edited['invoice_doctor_sms_format'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="creation_date">Invoice Creation Date Active</label>
                                        <select name="creation_date" id="creation_date" class="form-control">
                                            <option value="">Choose</option>
                                            <option
                                                value="Yes" @selected(old('creation_date', $edited['creation_date'] ?? '') == 'Yes')>
                                                Yes
                                            </option>
                                            <option
                                                value="No" @selected(old('creation_date', $edited['creation_date'] ?? '') == 'No')>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="footer_invoice">Footer Invoice</label>
                                        <textarea name="footer_invoice" id="footer_invoice"
                                                  class="form-control">{{ $edited['footer_invoice'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="footer_test_report_left">Footer Test Report(Left)</label>
                                        <textarea name="footer_test_report_left" id="footer_test_report_left"
                                                  class="form-control">{{ $edited['footer_test_report_left'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="footer_test_report_center">Footer Test Report(Center)</label>
                                        <textarea name="footer_test_report_center" id="footer_test_report_center"
                                                  class="form-control">{{ $edited['footer_test_report_center'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="footer_test_report_right">Footer Test Report(Right)</label>
                                        <textarea name="footer_test_report_right" id="footer_test_report_right"
                                                  class="form-control">{{ $edited['footer_test_report_right'] ?? '' }}</textarea>
                                    </div>

                                    <x-default.button class="float-end mt-2 btn-success">Update</x-default.button>

                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->

        <!-- partial -->
    </div>
@endsection

@push('scripts')
        <script>
            $('#footer_invoice').summernote({
                tabsize: 2,
                height: 150,
            })
            $('#address').summernote({
                tabsize: 2,
                height: 150,
            })
            $('#footer_test_report_left').summernote({
                tabsize: 2,
                height: 150,
            })
            $('#footer_test_report_center').summernote({
                tabsize: 2,
                height: 150,
            })
            $('#footer_test_report_right').summernote({
                tabsize: 2,
                height: 150,
            })
            $('#invoice_customer_sms_format').summernote({
                tabsize: 2,
                height: 150,
            })
            $('#invoice_doctor_sms_format').summernote({
                tabsize: 2,
                height: 150,
            })
        </script>
    @endpush

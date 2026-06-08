@extends('backend.layouts.master')
@section('title')
    Settings
@endsection

@push('styles')
    <style>
        .settings-page-header {
            background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
            color: #fff;
            border-radius: 14px;
            padding: 22px 24px;
            margin-bottom: 20px;
        }

        .settings-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 20px;
            align-items: start;
        }

        .settings-nav {
            position: sticky;
            top: 88px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .settings-nav-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 10px;
            padding: 0 8px;
        }

        .settings-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: #334155;
            text-decoration: none;
            font-size: 0.92rem;
            margin-bottom: 4px;
            transition: all 0.15s ease;
        }

        .settings-nav-link:hover,
        .settings-nav-link.active {
            background: #ecfeff;
            color: #0f766e;
        }

        .settings-nav-link i {
            width: 18px;
            text-align: center;
        }

        .settings-section {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            margin-bottom: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        }

        .settings-section-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
        }

        .settings-section-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ecfeff;
            color: #0f766e;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .settings-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .settings-section-desc {
            color: #64748b;
            font-size: 0.88rem;
            margin: 0;
        }

        .settings-section-body {
            padding: 22px;
        }

        .settings-field {
            margin-bottom: 1.1rem;
        }

        .settings-field label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .settings-help {
            display: block;
            margin-top: 6px;
            color: #64748b;
            font-size: 0.82rem;
        }

        .settings-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .settings-tag {
            background: #f1f5f9;
            color: #475569;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 0.78rem;
            font-family: Consolas, monospace;
        }

        .settings-logo-preview {
            margin-top: 10px;
            padding: 12px;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
            display: inline-block;
        }

        .settings-logo-preview img {
            max-height: 90px;
            max-width: 220px;
            object-fit: contain;
        }

        .settings-actions {
            position: sticky;
            bottom: 0;
            z-index: 20;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(8px);
        }

        @media (max-width: 991px) {
            .settings-layout {
                grid-template-columns: 1fr;
            }

            .settings-nav {
                position: static;
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                padding: 10px;
            }

            .settings-nav-title {
                width: 100%;
            }

            .settings-nav-link {
                margin-bottom: 0;
                padding: 8px 10px;
                font-size: 0.82rem;
            }
        }
    </style>
@endpush

@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            @include('backend.layouts.partials.message')

            <div class="settings-page-header">
                <h4 class="mb-1">System Settings</h4>
                <p class="mb-0 opacity-75">Manage hospital profile, finance mapping, SMS templates, attendance, and report footers from one place.</p>
            </div>

            @php
                $costCategories = \App\Models\CostCategory::where('branch_id', auth()->user()->branch_id)->get();
                $diagnosticCategories = $costCategories->where('type', 'diagnostic');
            @endphp

            <form method="post" action="{{ route($pageHeader['update_route']) }}" enctype="multipart/form-data" id="settings-form">
                @method('PUT')
                @csrf

                <div class="settings-layout">
                    <aside class="settings-nav">
                        <div class="settings-nav-title">Sections</div>
                        <a href="#section-organization" class="settings-nav-link active"><i class="fas fa-building"></i> Organization</a>
                        <a href="#section-finance" class="settings-nav-link"><i class="fas fa-coins"></i> Finance Mapping</a>
                        <a href="#section-invoice" class="settings-nav-link"><i class="fas fa-file-invoice"></i> Invoice</a>
                        <a href="#section-sms" class="settings-nav-link"><i class="fas fa-sms"></i> SMS Templates</a>
                        <a href="#section-appointment" class="settings-nav-link"><i class="fas fa-user-md"></i> Doctor Appointment</a>
                        <a href="#section-attendance" class="settings-nav-link"><i class="fas fa-calendar-check"></i> Attendance</a>
                        <a href="#section-reports" class="settings-nav-link"><i class="fas fa-file-medical"></i> Report Footers</a>
                    </aside>

                    <div class="settings-content">
                        {{-- Organization --}}
                        <section class="settings-section" id="section-organization">
                            <div class="settings-section-header">
                                <div class="settings-section-icon"><i class="fas fa-building"></i></div>
                                <div>
                                    <div class="settings-section-title">Organization Profile</div>
                                    <p class="settings-section-desc">Basic hospital identity used on invoices, reports, login page, and printed documents.</p>
                                </div>
                            </div>
                            <div class="settings-section-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="settings-field">
                                            <label for="company_name">Company / Hospital Name</label>
                                            <input type="text" name="company_name" id="company_name"
                                                   value="{{ $edited['company_name'] ?? '' }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="settings-field">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" id="email"
                                                   value="{{ $edited['email'] ?? '' }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="phone_one">Primary Phone</label>
                                            <input type="text" name="phone_one" id="phone_one"
                                                   value="{{ $edited['phone_one'] ?? '' }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="phone_two">Secondary Phone</label>
                                            <input type="text" name="phone_two" id="phone_two"
                                                   value="{{ $edited['phone_two'] ?? '' }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="settings-field">
                                            <label for="address">Address</label>
                                            <textarea name="address" id="address" class="form-control">{{ $edited['address'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="logo">Logo</label>
                                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                            <small class="settings-help">Recommended: PNG/JPG, transparent background, max width 300px.</small>
                                            @if(!empty($edited['logo']))
                                                <div class="settings-logo-preview">
                                                    <img src="{{ asset('images/' . $edited['logo']) }}" alt="Current Logo">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Finance --}}
                        <section class="settings-section" id="section-finance">
                            <div class="settings-section-header">
                                <div class="settings-section-icon"><i class="fas fa-coins"></i></div>
                                <div>
                                    <div class="settings-section-title">Finance & Cost Category Mapping</div>
                                    <p class="settings-section-desc">Link system actions to the correct cost categories for accounting and expense tracking.</p>
                                </div>
                            </div>
                            <div class="settings-section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="purchase_category">Purchase Category</label>
                                            <select name="purchase_category" id="purchase_category" class="form-select">
                                                <option value="">Choose category</option>
                                                @foreach($costCategories as $item)
                                                    <option value="{{ $item->id }}" @selected(old('purchase_category', $edited['purchase_category'] ?? '') == $item->id)>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="salary_category">Salary Category</label>
                                            <select name="salary_category" id="salary_category" class="form-select">
                                                <option value="">Choose category</option>
                                                @foreach($costCategories as $item)
                                                    <option value="{{ $item->id }}" @selected(old('salary_category', $edited['salary_category'] ?? '') == $item->id)>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="admit_refer_cost_category">Admit Refer Cost Category</label>
                                            <select name="admit_refer_cost_category" id="admit_refer_cost_category" class="form-select">
                                                <option value="">Choose category</option>
                                                @foreach($costCategories as $item)
                                                    <option value="{{ $item->id }}" @selected(old('admit_refer_cost_category', $edited['admit_refer_cost_category'] ?? '') == $item->id)>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="diagnostic_refer_cost_category">Diagnostic Refer Cost Category</label>
                                            <select name="diagnostic_refer_cost_category" id="diagnostic_refer_cost_category" class="form-select">
                                                <option value="">Choose category</option>
                                                @foreach($diagnosticCategories as $item)
                                                    <option value="{{ $item->id }}" @selected(old('diagnostic_refer_cost_category', $edited['diagnostic_refer_cost_category'] ?? '') == $item->id)>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Invoice --}}
                        <section class="settings-section" id="section-invoice">
                            <div class="settings-section-header">
                                <div class="settings-section-icon"><i class="fas fa-file-invoice"></i></div>
                                <div>
                                    <div class="settings-section-title">Invoice Settings</div>
                                    <p class="settings-section-desc">Control invoice SMS behavior, editable invoice date, and invoice footer content.</p>
                                </div>
                            </div>
                            <div class="settings-section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="invoice_sms">Send Invoice SMS</label>
                                            <select name="invoice_sms" id="invoice_sms" class="form-select">
                                                <option value="">Choose</option>
                                                <option value="Yes" @selected(old('invoice_sms', $edited['invoice_sms'] ?? '') == 'Yes')>Yes</option>
                                                <option value="No" @selected(old('invoice_sms', $edited['invoice_sms'] ?? '') == 'No')>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="creation_date">Allow Invoice Creation Date Edit</label>
                                            <select name="creation_date" id="creation_date" class="form-select">
                                                <option value="">Choose</option>
                                                <option value="Yes" @selected(old('creation_date', $edited['creation_date'] ?? '') == 'Yes')>Yes</option>
                                                <option value="No" @selected(old('creation_date', $edited['creation_date'] ?? '') == 'No')>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="settings-field">
                                            <label for="footer_invoice">Invoice Footer</label>
                                            <textarea name="footer_invoice" id="footer_invoice" class="form-control">{{ $edited['footer_invoice'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- SMS Templates --}}
                        <section class="settings-section" id="section-sms">
                            <div class="settings-section-header">
                                <div class="settings-section-icon"><i class="fas fa-sms"></i></div>
                                <div>
                                    <div class="settings-section-title">SMS Templates</div>
                                    <p class="settings-section-desc">Customize automated SMS messages. Use placeholders exactly as shown below.</p>
                                </div>
                            </div>
                            <div class="settings-section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="pc_payment_sms">PC Payment SMS</label>
                                            <select name="pc_payment_sms" id="pc_payment_sms" class="form-select">
                                                <option value="">Choose</option>
                                                <option value="Yes" @selected(old('pc_payment_sms', $edited['pc_payment_sms'] ?? '') == 'Yes')>Yes</option>
                                                <option value="No" @selected(old('pc_payment_sms', $edited['pc_payment_sms'] ?? '') == 'No')>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="invoice_customer_sms_format">Customer SMS Template</label>
                                    <div class="settings-tags">
                                        <span class="settings-tag">{invoice_number}</span>
                                        <span class="settings-tag">{due_amount}</span>
                                        <span class="settings-tag">{advance_amount}</span>
                                        <span class="settings-tag">{patient_name}</span>
                                        <span class="settings-tag">{ref_name}</span>
                                    </div>
                                    <textarea name="invoice_customer_sms_format" id="invoice_customer_sms_format" rows="5"
                                              class="form-control mt-2">{{ $edited['invoice_customer_sms_format'] ?? 'আমাদের হাসপাতালে আসার জন্য আপনাকে ধন্যবাদ । আইডি {invoice_number} বাকি {due_amount} এডভান্স {advance_amount} ২৪ঘন্টা  সেবায় আমরা আছি আপনার পাশে।' }}</textarea>
                                </div>

                                <div class="settings-field">
                                    <label for="invoice_doctor_sms_format">Doctor / Refer SMS Template</label>
                                    <div class="settings-tags">
                                        <span class="settings-tag">{invoice_number}</span>
                                        <span class="settings-tag">{amount}</span>
                                        <span class="settings-tag">{patient_name}</span>
                                        <span class="settings-tag">{dr_name}</span>
                                    </div>
                                    <textarea name="invoice_doctor_sms_format" id="invoice_doctor_sms_format" rows="5"
                                              class="form-control mt-2">{{ $edited['invoice_doctor_sms_format'] ?? 'আমাদের হাসপাতালে রুগি পাঠানোর জন্য অনেক ধন্যবাদ। রুগির নাম {patient_name}, আইডি {invoice_number} । আপনার সহযোগিতা সর্বদা কামনা করি।' }}</textarea>
                                </div>

                                <div class="settings-field">
                                    <label for="invoice_doctor_refer_sms_format">Doctor & Refer SMS (Extra)</label>
                                    <div class="settings-tags">
                                        <span class="settings-tag">{amount}</span>
                                    </div>
                                    <textarea name="invoice_doctor_refer_sms_format" id="invoice_doctor_refer_sms_format" rows="4"
                                              class="form-control mt-2">{{ $edited['invoice_doctor_refer_sms_format'] ?? '' }}</textarea>
                                </div>

                                <div class="settings-field mb-0">
                                    <label for="refer_payment_sms_format">Refer Payment SMS Template</label>
                                    <div class="settings-tags">
                                        <span class="settings-tag">{amount}</span>
                                    </div>
                                    <textarea name="refer_payment_sms_format" id="refer_payment_sms_format" rows="4"
                                              class="form-control mt-2">{{ $edited['refer_payment_sms_format'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </section>

                        {{-- Doctor Appointment --}}
                        <section class="settings-section" id="section-appointment">
                            <div class="settings-section-header">
                                <div class="settings-section-icon"><i class="fas fa-user-md"></i></div>
                                <div>
                                    <div class="settings-section-title">Doctor Appointment</div>
                                    <p class="settings-section-desc">Enable doctor serial-based appointments and configure the SMS format sent to patients.</p>
                                </div>
                            </div>
                            <div class="settings-section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="doctors_appointment">Doctor Serial Appointment</label>
                                            <select name="doctors_appointment" id="doctors_appointment" class="form-select">
                                                <option value="">Choose</option>
                                                <option value="Yes" @selected(old('doctors_appointment', $edited['doctors_appointment'] ?? '') == 'Yes')>Yes</option>
                                                <option value="No" @selected(old('doctors_appointment', $edited['doctors_appointment'] ?? '') == 'No')>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="settings-field mb-0">
                                    <label for="doctors_appointment_sms_format">Appointment SMS Template</label>
                                    <div class="settings-tags">
                                        <span class="settings-tag">{patient_name}</span>
                                        <span class="settings-tag">{serial}</span>
                                        <span class="settings-tag">{date}</span>
                                        <span class="settings-tag">{time}</span>
                                        <span class="settings-tag">{doctor}</span>
                                    </div>
                                    <textarea name="doctors_appointment_sms_format" id="doctors_appointment_sms_format" rows="5"
                                              class="form-control mt-2">{{ $edited['doctors_appointment_sms_format'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </section>

                        {{-- Attendance --}}
                        <section class="settings-section" id="section-attendance">
                            <div class="settings-section-header">
                                <div class="settings-section-icon"><i class="fas fa-calendar-check"></i></div>
                                <div>
                                    <div class="settings-section-title">Attendance & HR</div>
                                    <p class="settings-section-desc">Configure how employee attendance is recorded from devices and used in salary calculations.</p>
                                </div>
                            </div>
                            <div class="settings-section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="attendance_mode">Attendance Mode</label>
                                            <select name="attendance_mode" id="attendance_mode" class="form-select">
                                                <option value="standard" @selected(old('attendance_mode', $edited['attendance_mode'] ?? 'standard') == 'standard')>
                                                    Standard (Daily IN/OUT)
                                                </option>
                                                <option value="hourly" @selected(old('attendance_mode', $edited['attendance_mode'] ?? 'standard') == 'hourly')>
                                                    Hourly (One row per hour)
                                                </option>
                                            </select>
                                            <small class="settings-help">Controls how RFID/fingerprint attendance is stored for this branch.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="settings-field">
                                            <label for="attendance_grace_minutes">Hourly Grace Minutes</label>
                                            <input type="number" min="0" max="59" name="attendance_grace_minutes" id="attendance_grace_minutes"
                                                   value="{{ old('attendance_grace_minutes', $edited['attendance_grace_minutes'] ?? 0) }}"
                                                   placeholder="e.g. 5 or 10" class="form-control">
                                            <small class="settings-help">Suggested: 5–10 minutes. Use 0 for strict hourly cutoff.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Report Footers --}}
                        <section class="settings-section" id="section-reports">
                            <div class="settings-section-header">
                                <div class="settings-section-icon"><i class="fas fa-file-medical"></i></div>
                                <div>
                                    <div class="settings-section-title">Test Report Footers</div>
                                    <p class="settings-section-desc">Footer text shown on printed lab/test reports in left, center, and right positions.</p>
                                </div>
                            </div>
                            <div class="settings-section-body">
                                <div class="settings-field">
                                    <label for="footer_test_report_left">Left Footer</label>
                                    <textarea name="footer_test_report_left" id="footer_test_report_left" class="form-control">{{ $edited['footer_test_report_left'] ?? '' }}</textarea>
                                </div>
                                <div class="settings-field">
                                    <label for="footer_test_report_center">Center Footer</label>
                                    <textarea name="footer_test_report_center" id="footer_test_report_center" class="form-control">{{ $edited['footer_test_report_center'] ?? '' }}</textarea>
                                </div>
                                <div class="settings-field mb-0">
                                    <label for="footer_test_report_right">Right Footer</label>
                                    <textarea name="footer_test_report_right" id="footer_test_report_right" class="form-control">{{ $edited['footer_test_report_right'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </section>

                        <div class="settings-actions">
                            <div class="text-muted small">
                                <i class="fas fa-info-circle"></i> Changes apply to the current branch only.
                            </div>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-1"></i> Save All Settings
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const richEditors = [
                '#footer_invoice',
                '#address',
                '#footer_test_report_left',
                '#footer_test_report_center',
                '#footer_test_report_right',
                '#invoice_customer_sms_format',
                '#invoice_doctor_sms_format',
                '#invoice_doctor_refer_sms_format',
            ];

            richEditors.forEach(function (selector) {
                $(selector).summernote({ tabsize: 2, height: 140 });
            });

            const navLinks = document.querySelectorAll('.settings-nav-link');
            const sections = document.querySelectorAll('.settings-section');

            navLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    navLinks.forEach(function (item) { item.classList.remove('active'); });
                    this.classList.add('active');
                });
            });

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            const id = entry.target.getAttribute('id');
                            navLinks.forEach(function (link) {
                                link.classList.toggle('active', link.getAttribute('href') === '#' + id);
                            });
                        }
                    });
                }, { rootMargin: '-20% 0px -65% 0px', threshold: 0.1 });

                sections.forEach(function (section) { observer.observe(section); });
            }
        });
    </script>
@endpush

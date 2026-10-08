@extends('backend.layouts.master')

@section('title')
    Suspicious Activity Report
@endsection

@php
    $tk = fn ($value) => '৳' . number_format((float) $value, 2);
    $resetTime = fn ($at) => \Carbon\Carbon::parse($at, 'UTC')->timezone('Asia/Dhaka')->format('d M Y h:i A');
    $openKeys = fn ($keys) => collect($keys)->reject(fn ($key) => isset($hiddenKeys[$key]))->values()->all();
@endphp

@section('admin-content')
    <div class="container-fluid py-3">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h4 class="mb-1"><i class="fas fa-user-secret text-danger"></i> Suspicious Activity Report</h4>
                    <p class="text-muted mb-0">
                        {{ \Carbon\Carbon::parse($start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($end)->format('d M Y') }}.
                        কোন staff বেশি edit, delete, discount বা cash কম দেখাচ্ছে তা এখানে দেখা যায়।
                    </p>
                </div>
                <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="date" name="start_date" value="{{ $start }}" class="form-control form-control-sm">
                    <input type="date" name="end_date" value="{{ $end }}" class="form-control form-control-sm">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="show_hidden" value="1" id="showHidden" @checked($showHidden) onchange="this.form.submit()">
                        <label class="form-check-label small" for="showHidden">সরানোগুলোও দেখান ({{ $hiddenCount }})</label>
                    </div>
                    <button class="btn btn-sm btn-primary">Show</button>
                </form>
                <form method="POST" action="{{ route('admin.security.report.reset') }}"
                      onsubmit="return confirm('এখন পর্যন্ত report-এর সব কিছু (Staff Summary, ফাঁক, discount, edit, failed login) সরাবেন?\n\nInvoice, payment, Trash/Audit বা অন্য কোনো আসল data মুছবে না। পরে Undo করা যাবে।')">
                    @csrf
                    <input type="hidden" name="mode" value="all">
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-broom"></i> পুরো Report Clear</button>
                </form>
            </div>
        </div>

        @if($resets['all'] || $resets['staff'])
            <div class="alert alert-info py-2">
                @if($resets['all'])
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span><i class="fas fa-broom"></i> পুরো report clear করা হয়েছে <strong>{{ $resetTime($resets['all']['at']) }}</strong>
                            ({{ $resetAdmins[$resets['all']['by']] ?? 'Unknown' }})। এর আগের কিছু গোনা হচ্ছে না।</span>
                        <form method="POST" action="{{ route('admin.security.report.reset') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="mode" value="undo_all">
                            <button class="btn btn-sm btn-outline-success py-0"><i class="fas fa-rotate-left"></i> Undo</button>
                        </form>
                    </div>
                @endif
                @foreach($resets['staff'] as $resetAdminId => $reset)
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span><i class="fas fa-eraser"></i> <strong>{{ $resetAdmins[$resetAdminId] ?? 'Unknown' }}</strong>-এর হিসাব clear করা হয়েছে
                            {{ $resetTime($reset['at']) }} ({{ $resetAdmins[$reset['by']] ?? 'Unknown' }})।</span>
                        <form method="POST" action="{{ route('admin.security.report.reset') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="mode" value="undo_staff">
                            <input type="hidden" name="admin_id" value="{{ $resetAdminId }}">
                            <button class="btn btn-sm btn-outline-success py-0"><i class="fas fa-rotate-left"></i> Undo</button>
                        </form>
                    </div>
                @endforeach
                <div class="small text-muted">"সরানোগুলোও দেখান" টিক দিলে clear করা সব কিছু আগের মতো দেখা যাবে। আসল data কিছুই মোছেনি।</div>
            </div>
        @endif

        @if(!$auditReady)
            <div class="alert alert-warning">Audit table install করা নেই, তাই edit/delete-এর হিসাব দেখানো যাচ্ছে না।</div>
        @endif

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body table-responsive">
                <h5 class="mb-1">Staff-wise Summary</h5>
                <p class="text-muted small">সবচেয়ে ঝুঁকিপূর্ণ কাজ করা staff উপরে। Failed login এই সময়ে: <strong>{{ $failedLogins }}</strong>
                    (<a href="{{ route('admin.security.logins', ['event' => 'failed']) }}">দেখুন</a>)</p>
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Staff</th>
                        <th class="text-end">Bills</th>
                        <th class="text-end">Bill Total</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Collected</th>
                        <th class="text-end">Edits</th>
                        <th class="text-end">Bill কমিয়েছে</th>
                        <th class="text-end">Deletes</th>
                        <th class="text-end">Cancel Req</th>
                        <th class="text-end">Cash Short</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($staff as $row)
                        @php $risk = $row['risk']; @endphp
                        <tr class="{{ $risk >= 8 ? 'table-danger' : ($risk >= 4 ? 'table-warning' : '') }}">
                            <td>
                                {{ $row['admin']->name ?? 'Unknown' }}
                                @if($risk >= 8)
                                    <span class="badge bg-danger">High risk</span>
                                @elseif($risk >= 4)
                                    <span class="badge bg-warning text-dark">Watch</span>
                                @endif
                            </td>
                            <td class="text-end">{{ $row['bills'] }}</td>
                            <td class="text-end">{{ $tk($row['total']) }}</td>
                            <td class="text-end {{ $row['discount_percent'] >= 20 ? 'text-danger fw-bold' : '' }}">
                                {{ $tk($row['discount']) }} <span class="small text-muted">({{ $row['discount_percent'] }}%)</span>
                            </td>
                            <td class="text-end">{{ $tk($row['collected']) }}</td>
                            <td class="text-end">{{ $row['edits'] }}</td>
                            <td class="text-end {{ $row['reduced'] > 0 ? 'text-danger fw-bold' : '' }}">{{ $row['reduced'] > 0 ? $tk($row['reduced']) : '—' }}</td>
                            <td class="text-end {{ $row['deletes'] > 0 ? 'text-danger fw-bold' : '' }}">{{ $row['deletes'] }}</td>
                            <td class="text-end">{{ $row['cancel_requests'] }}</td>
                            <td class="text-end {{ $row['cash_short'] > 0 ? 'text-danger fw-bold' : '' }}">{{ $row['cash_short'] > 0 ? $tk($row['cash_short']) : '—' }}</td>
                            <td class="text-end text-nowrap">
                                @if($row['admin'])
                                    <a href="{{ route('admin.audit-logs.index', ['admin_id' => $row['admin']->id, 'start_date' => $start, 'end_date' => $end]) }}"
                                       class="btn btn-sm btn-outline-secondary">Trash</a>
                                    @unless(isset($resets['staff'][$row['admin']->id]) && $showHidden)
                                        <form method="POST" action="{{ route('admin.security.report.reset') }}" class="d-inline"
                                              onsubmit="return confirm('এই staff-এর এখন পর্যন্ত সব হিসাব report থেকে সরাবেন?\n\nশুধু report-এ দেখানো বন্ধ হবে, invoice/payment/Trash কিছুই মুছবে না। পরে Undo করা যাবে।')">
                                            @csrf
                                            <input type="hidden" name="mode" value="staff">
                                            <input type="hidden" name="admin_id" value="{{ $row['admin']->id }}">
                                            <button class="btn btn-sm btn-outline-secondary" title="এই staff-এর হিসাব report থেকে সরান"><i class="fas fa-xmark"></i></button>
                                        </form>
                                    @endunless
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted py-4">এই সময়ে কোনো কাজ নেই।</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="mb-1"><i class="fas fa-hashtag text-warning"></i> Invoice নম্বরে ফাঁক</h5>
                        <p class="text-muted small">নম্বর বাদ পড়া মানে ঐ invoice মুছে ফেলা হয়েছে। Trash-এ রেকর্ড না থাকলে সেটা সবচেয়ে সন্দেহজনক।</p>
                        @forelse($gaps as $gap)
                            @php $gapKeys = $openKeys(collect($gap['missing'])->pluck('key')); @endphp
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <strong>{{ $gap['month'] }}</strong>
                                        <span class="text-muted small">(শেষ নম্বর {{ $gap['last'] }}, বাদ পড়েছে {{ $gap['missing_count'] }}টি)</span>
                                    </div>
                                    @if(count($gapKeys) > 1)
                                        @include('backend.pages.security.partials.report-dismiss', ['keys' => $gapKeys, 'label' => ' সব সরান', 'confirm' => 'এই মাসের ' . count($gapKeys) . 'টি নম্বর report থেকে সরাবেন? Trash/Audit মুছবে না।'])
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach($gap['missing'] as $missing)
                                        <span class="d-inline-flex align-items-center gap-1 {{ isset($hiddenKeys[$missing['key']]) ? 'opacity-50' : '' }}">
                                            @if($missing['log'])
                                                <a href="{{ route('admin.audit-logs.show', $missing['log']->id) }}" class="badge bg-secondary text-decoration-none"
                                                   title="Deleted by {{ $missing['log']->admin->name ?? 'Unknown' }}">
                                                    {{ $missing['number'] }} <i class="fas fa-trash-restore"></i>
                                                </a>
                                            @else
                                                <span class="badge bg-danger" title="Trash-এ কোনো রেকর্ড নেই">{{ $missing['number'] }} ?</span>
                                            @endif
                                            @include('backend.pages.security.partials.report-dismiss', ['keys' => [$missing['key']], 'class' => 'py-0 px-1'])
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-success"><i class="fas fa-check-circle"></i> কোনো ফাঁক নেই।</div>
                        @endforelse
                        <div class="small text-muted mt-2">
                            <span class="badge bg-secondary">ধূসর</span> = Trash-এ আছে (কে মুছেছে দেখতে ক্লিক করুন) ·
                            <span class="badge bg-danger">লাল ?</span> = Trash চালুর আগে মুছেছে বা রেকর্ড নেই
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body table-responsive">
                        @php $sectionKeys = $openKeys($reducedEdits->take(30)->map(fn ($log) => 'a' . $log->id)); @endphp
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h5 class="mb-1"><i class="fas fa-arrow-trend-down text-danger"></i> Edit করে Bill কমানো</h5>
                            @if(count($sectionKeys) > 1)
                                @include('backend.pages.security.partials.report-dismiss', ['keys' => $sectionKeys, 'label' => ' সব সরান', 'confirm' => 'এই তালিকার ' . count($sectionKeys) . 'টি report থেকে সরাবেন? Trash/Audit মুছবে না।'])
                            @endif
                        </div>
                        <p class="text-muted small">টাকা নিয়ে পরে bill কমানো — সবচেয়ে কমন চুরি।</p>
                        <table class="table table-sm align-middle">
                            <thead><tr><th>When</th><th>Invoice</th><th>By</th><th class="text-end">আগে → পরে</th><th></th></tr></thead>
                            <tbody>
                            @forelse($reducedEdits->take(30) as $log)
                                <tr class="{{ isset($hiddenKeys['a' . $log->id]) ? 'opacity-50' : '' }}">
                                    <td class="small">{{ $log->created_at->timezone('Asia/Dhaka')->format('d M h:i A') }}</td>
                                    <td>{{ $log->old_values['invoice_number'] ?? '#' . $log->auditable_id }}</td>
                                    <td>{{ $log->admin->name ?? 'Unknown' }}</td>
                                    <td class="text-end text-danger">{{ $tk($log->old_values['total_amount'] ?? 0) }} → {{ $tk($log->new_values['total_amount'] ?? 0) }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        @include('backend.pages.security.partials.report-dismiss', ['keys' => ['a' . $log->id]])
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">কোনো bill কমানো হয়নি।</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body table-responsive">
                        @php $sectionKeys = $openKeys($highDiscounts->map(fn ($invoice) => 'i' . $invoice->id)); @endphp
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h5 class="mb-1"><i class="fas fa-percent text-warning"></i> বেশি Discount ({{ $threshold > 0 ? $threshold . '%+' : 'বন্ধ' }})</h5>
                            @if(count($sectionKeys) > 1)
                                @include('backend.pages.security.partials.report-dismiss', ['keys' => $sectionKeys, 'label' => ' সব সরান', 'confirm' => 'এই তালিকার ' . count($sectionKeys) . 'টি report থেকে সরাবেন? Invoice মুছবে না।'])
                            @endif
                        </div>
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Date</th><th>Invoice</th><th>By</th><th>Discount By</th><th class="text-end">Discount</th><th></th></tr></thead>
                            <tbody>
                            @forelse($highDiscounts as $invoice)
                                @php $gross = (float) $invoice->total_amount + (float) $invoice->discount_amount; @endphp
                                <tr class="{{ isset($hiddenKeys['i' . $invoice->id]) ? 'opacity-50' : '' }}">
                                    <td class="small">{{ \Carbon\Carbon::parse($invoice->creation_date)->format('d M') }}</td>
                                    <td><a href="{{ route('admin.invoices.show', $invoice->id) }}">{{ $invoice->invoice_number }}</a></td>
                                    <td>{{ $invoice->admin->name ?? 'Unknown' }}</td>
                                    <td class="small">{{ $invoice->discount_by }}</td>
                                    <td class="text-end">{{ $tk($invoice->discount_amount) }}
                                        <span class="small text-muted">({{ $gross > 0 ? round($invoice->discount_amount / $gross * 100, 1) : 0 }}%)</span></td>
                                    <td class="text-end">@include('backend.pages.security.partials.report-dismiss', ['keys' => ['i' . $invoice->id]])</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">কোনো বেশি discount নেই।</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body table-responsive">
                        @php $sectionKeys = $openKeys($lateEdits->take(30)->map(fn ($log) => 'a' . $log->id)); @endphp
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h5 class="mb-1"><i class="fas fa-clock-rotate-left text-info"></i> পুরনো Invoice পরে Edit</h5>
                            @if(count($sectionKeys) > 1)
                                @include('backend.pages.security.partials.report-dismiss', ['keys' => $sectionKeys, 'label' => ' সব সরান', 'confirm' => 'এই তালিকার ' . count($sectionKeys) . 'টি report থেকে সরাবেন? Trash/Audit মুছবে না।'])
                            @endif
                        </div>
                        <p class="text-muted small">Invoice যেদিন হয়েছে তার পরের কোনো দিনে edit।</p>
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Edited</th><th>Invoice (তারিখ)</th><th>By</th><th>Reason</th><th></th></tr></thead>
                            <tbody>
                            @forelse($lateEdits->take(30) as $log)
                                <tr class="{{ isset($hiddenKeys['a' . $log->id]) ? 'opacity-50' : '' }}">
                                    <td class="small">{{ $log->created_at->timezone('Asia/Dhaka')->format('d M h:i A') }}</td>
                                    <td>{{ $log->old_values['invoice_number'] ?? '#' . $log->auditable_id }}
                                        <span class="small text-muted">({{ \Carbon\Carbon::parse($log->old_values['creation_date'])->format('d M') }})</span></td>
                                    <td>{{ $log->admin->name ?? 'Unknown' }}</td>
                                    <td class="small">{{ \Illuminate\Support\Str::limit($log->reason ?? '—', 40) }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        @include('backend.pages.security.partials.report-dismiss', ['keys' => ['a' . $log->id]])
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">কোনো পুরনো invoice edit হয়নি।</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

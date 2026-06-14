@php
    $userGuard = Auth::guard('admin')->user();
@endphp
<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header topbar-navbar">
            <div class="topbar-item topbar-menu-wrap flex-shrink-0">
                <button type="button" class="button-toggle-menu topbar-button topbar-menu-btn" aria-label="Open menu">
                    <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                </button>
            </div>

            <div class="topbar-left d-flex align-items-center">
                @include('backend.layouts.partials.topbar-quick-links', ['topbarQuickLinksMode' => 'desktop'])

                @if($userGuard->can('users.index'))
                <div class="topbar-patient-search d-none d-md-block">
                    <div class="topbar-search-wrap">
                        <i class="fas fa-search topbar-search-icon"></i>
                        <input type="search" id="global-patient-search" class="topbar-search-input"
                               placeholder="Search patient (name / phone)..." autocomplete="off">
                        <div id="global-patient-search-results" class="topbar-search-dropdown"></div>
                    </div>
                </div>
                @endif
            </div>

            <div class="topbar-right d-flex align-items-center">
                <span class="badge bg-danger topbar-sms-badge">
                    {{ \App\Models\SmsBalance::where('branch_id',auth()->user()->branch_id)->first()->balance ?? 0 }} Point's
                </span>
  <!-- Category -->
                <div class="dropdown topbar-item d-none d-lg-flex">
                    <button type="button" class="topbar-button" data-toggle="fullscreen">
                        <iconify-icon icon="solar:full-screen-broken"
                                      class="fs-24 align-middle fullscreen"></iconify-icon>
                        <iconify-icon icon="solar:quit-full-screen-broken"
                                      class="fs-24 align-middle quit-fullscreen"></iconify-icon>
                    </button>
                </div>
                <!-- Notification -->
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button position-relative"
                            id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <iconify-icon icon="solar:bell-bing-broken" class="fs-24 align-middle"></iconify-icon>
                        <span
                            class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">{{ expairyAlertNotificationCount() }}<span
                                class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end"
                         aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                        <small>Clear All</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 280px;">
                            <!-- Item -->
                            <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom text-wrap">
                                <div class="d-flex">

                                    <div class="flex-grow-1">
                                        {!! expairyAlertNotification() !!}
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="text-center py-3">
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm">View All Notification <i
                                    class="bx bx-right-arrow-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>

                @if ( Auth::guard('admin')->user()->can('reports.index'))
                    <button id="openBalanceModal" class="btn btn-info btn-sm" title="Balance">
                        <i class="fas fa-eye"></i>
                    </button>
                @endif
                <!-- Modal Structure -->
                <div id="balanceModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Current Month Summary</h5>
                                <button type="button" class="btn-close close-modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {!! currentBalanceMonth() !!}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary close-modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Theme Setting -->
                <div class="topbar-item d-none d-md-flex">
                    <button type="button" class="topbar-button" id="theme-settings-btn" data-bs-toggle="offcanvas"
                            data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
                        <iconify-icon icon="solar:settings-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <div class="topbar-user-chip" title="Logged in as {{ $userGuard->name }}">
                    <i class="fas fa-user-shield"></i>
                    <div class="topbar-user-text">
                        <span class="topbar-user-label">Logged in as</span>
                        <strong class="topbar-user-name">{{ $userGuard->name }}</strong>
                    </div>
                </div>

                <a href="{{ route('admin.logout.submit') }}" class="btn btn-outline-danger btn-sm topbar-logout-btn" title="Logout">
                    <i class="bx bx-log-out"></i>
                    <span class="d-none d-xl-inline">Logout</span>
                </a>
            </div>
        </div>

        @include('backend.layouts.partials.topbar-quick-links', ['topbarQuickLinksMode' => 'mobile'])
    </div>
</header>



<style>
    .topbar-navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        min-height: 64px;
    }

    .topbar-left {
        flex: 1 1 auto;
        min-width: 0;
        gap: 8px;
    }

    .topbar-patient-search {
        flex: 0 1 280px;
        min-width: 180px;
        max-width: 320px;
    }

    .topbar-search-wrap {
        position: relative;
    }

    .topbar-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.85rem;
        pointer-events: none;
    }

    .topbar-search-input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 8px 14px 8px 34px;
        font-size: 0.85rem;
        background: #f8fafc;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .topbar-search-input:focus {
        outline: none;
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #fff;
    }

    .topbar-search-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
        z-index: 1080;
        max-height: 320px;
        overflow-y: auto;
    }

    .topbar-search-dropdown.show { display: block; }

    .topbar-search-item {
        display: block;
        padding: 10px 14px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.12s ease;
    }

    .topbar-search-item:hover { background: #eff6ff; color: inherit; }
    .topbar-search-item:last-child { border-bottom: none; }

    .topbar-search-item-name { font-weight: 700; font-size: 0.88rem; color: #0f172a; }
    .topbar-search-item-sub { font-size: 0.75rem; color: #64748b; }

    .topbar-menu-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    }

    .topbar-menu-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .tb-quick-nav {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        flex: 1 1 auto;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding: 2px 4px;
    }

    .tb-quick-nav::-webkit-scrollbar { display: none; }

    .tb-quick-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 999px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
        border: 1px solid transparent;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .tb-quick-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        color: inherit;
    }

    .tb-quick-link-icon {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
    }

    .tb-quick-link--patients { background: #f1f5f9; color: #334155; border-color: #e2e8f0; }
    .tb-quick-link--patients .tb-quick-link-icon { background: #334155; color: #fff; }
    .tb-quick-link--invoices { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .tb-quick-link--invoices .tb-quick-link-icon { background: #059669; color: #fff; }
    .tb-quick-link--admits { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .tb-quick-link--admits .tb-quick-link-icon { background: #64748b; color: #fff; }
    .tb-quick-link--recepts { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .tb-quick-link--recepts .tb-quick-link-icon { background: #f59e0b; color: #fff; }
    .tb-quick-link--lab { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
    .tb-quick-link--lab .tb-quick-link-icon { background: #4f46e5; color: #fff; }
    .tb-quick-link--cost { background: #faf5ff; color: #7e22ce; border-color: #e9d5ff; }
    .tb-quick-link--cost .tb-quick-link-icon { background: #0f172a; color: #fff; }

    .tb-quick-link.is-active {
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
    }

    .tb-quick-strip {
        border-top: 1px solid #eef2f7;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        padding: 8px 0 10px;
        margin: 0 -0.65rem;
        padding-left: 0.65rem;
        padding-right: 0.65rem;
    }

    .tb-quick-strip-scroll {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding: 0 0.65rem;
    }

    .tb-quick-strip-scroll::-webkit-scrollbar { display: none; }

    .tb-quick-chip {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-width: 68px;
        padding: 8px 10px;
        border-radius: 14px;
        text-decoration: none;
        font-size: 0.68rem;
        font-weight: 700;
        line-height: 1.1;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    }

    .tb-quick-chip i {
        font-size: 1rem;
    }

    .tb-quick-chip--patients i { color: #334155; }
    .tb-quick-chip--invoices i { color: #059669; }
    .tb-quick-chip--admits i { color: #64748b; }
    .tb-quick-chip--recepts i { color: #d97706; }
    .tb-quick-chip--lab i { color: #4f46e5; }
    .tb-quick-chip--cost i { color: #7e22ce; }

    .tb-quick-chip.is-active {
        border-color: #93c5fd;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .topbar-right {
        flex: 0 0 auto;
        gap: 6px;
        margin-left: auto;
        padding-left: 8px;
    }

    .topbar-sms-badge {
        white-space: nowrap;
        flex-shrink: 0;
    }

    .topbar-logout-btn {
        white-space: nowrap;
        flex-shrink: 0;
    }

    .topbar-user-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 6px 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #ecfeff 0%, #f0fdf4 100%);
        border: 1px solid rgba(14, 116, 144, 0.18);
        color: #0f766e;
        flex-shrink: 0;
        max-width: 220px;
    }

    .topbar-user-chip > i {
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .topbar-user-text {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
        min-width: 0;
    }

    .topbar-user-label {
        font-size: 0.68rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .topbar-user-name {
        font-size: 0.92rem;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width: 1400px) {
        .tb-quick-link {
            font-size: 0.76rem;
            padding: 6px 10px;
        }
    }

    @media (max-width: 991.98px) {
        .topbar-navbar {
            display: grid !important;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 8px;
        }

        .topbar-menu-wrap {
            grid-column: 1;
        }

        .topbar-left {
            grid-column: 2;
            min-width: 0;
        }

        .topbar-right {
            grid-column: 3;
            margin-left: 0;
        }
    }

    .quick-calc {
        background: #0f172a;
        border-radius: 12px;
        padding: 12px;
        color: #e5e7eb;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.4);
    }

    .quick-calc-display {
        background: #020617;
        color: #e5e7eb;
        border-radius: 8px;
        border: 1px solid #1e293b;
        text-align: right;
        font-size: 1.2rem;
        padding: 8px 10px;
    }

    .quick-calc-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-top: 8px;
    }

    .quick-calc-btn {
        border: none;
        border-radius: 8px;
        padding: 8px 0;
        font-size: 1rem;
        font-weight: 600;
        background: #1f2937;
        color: #f9fafb;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.5);
        transition: all 0.15s ease-in-out;
    }

    .quick-calc-btn:hover {
        background: #374151;
        transform: translateY(-1px);
    }

    .quick-calc-btn-eq {
        background: #16a34a;
    }

    .quick-calc-btn-eq:hover {
        background: #22c55e;
    }

    .quick-calc-btn-zero {
        grid-column: span 2;
    }
</style>

<!-- Right Sidebar (Theme Settings) -->
<div>
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
            <h5 class="text-white m-0">Theme Settings</h5>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-0">
            <div data-simplebar class="h-100">
                <div class="p-3 settings-bar">

                    <div>
                        <h5 class="mb-3 font-16 fw-semibold">Color Scheme</h5>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-light"
                                   value="light">
                            <label class="form-check-label" for="layout-color-light">Light</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-color-dark"
                                   value="dark">
                            <label class="form-check-label" for="layout-color-dark">Dark</label>
                        </div>
                    </div>

                    <div>
                        <h5 class="my-3 font-16 fw-semibold">Topbar Color</h5>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-topbar-color"
                                   id="topbar-color-light" value="light">
                            <label class="form-check-label" for="topbar-color-light">Light</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-dark"
                                   value="dark">
                            <label class="form-check-label" for="topbar-color-dark">Dark</label>
                        </div>
                    </div>


                    <div>
                        <h5 class="my-3 font-16 fw-semibold">Menu Color</h5>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-light"
                                   value="light">
                            <label class="form-check-label" for="leftbar-color-light">
                                Light
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-color" id="leftbar-color-dark"
                                   value="dark">
                            <label class="form-check-label" for="leftbar-color-dark">
                                Dark
                            </label>
                        </div>
                    </div>

                    <div>
                        <h5 class="my-3 font-16 fw-semibold">Sidebar Size</h5>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-default"
                                   value="default">
                            <label class="form-check-label" for="leftbar-size-default">
                                Default
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-size-small"
                                   value="condensed">
                            <label class="form-check-label" for="leftbar-size-small">
                                Condensed
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size" id="leftbar-hidden"
                                   value="hidden">
                            <label class="form-check-label" for="leftbar-hidden">
                                Hidden
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size"
                                   id="leftbar-size-small-hover-active" value="sm-hover-active">
                            <label class="form-check-label" for="leftbar-size-small-hover-active">
                                Small Hover Active
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="data-menu-size"
                                   id="leftbar-size-small-hover" value="sm-hover">
                            <label class="form-check-label" for="leftbar-size-small-hover">
                                Small Hover
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3 text-center">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-danger w-100" id="reset-layout">Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('backend/assets/vendor/jsvectormap/jquery.min.js') }}"></script>


<script>
    $(document).ready(function () {
        $('#openBalanceModal').on('click', function () {
            $('#balanceModal').fadeIn().addClass('show').css('display', 'block');
        });

        $('.close-modal').on('click', function () {
            $('#balanceModal').fadeOut().removeClass('show').css('display', 'none');
        });

        // Optional: close modal when clicking outside modal-content
        $('#balanceModal').on('click', function (e) {
            if ($(e.target).is('#balanceModal')) {
                $(this).fadeOut().removeClass('show').css('display', 'none');
            }
        });

    });
</script>

@if($userGuard->can('users.index'))
<script>
(function () {
    var input = document.getElementById('global-patient-search');
    var dropdown = document.getElementById('global-patient-search-results');
    if (!input || !dropdown) return;

    var searchUrl = @json(route('admin.patients.search'));
    var timer = null;

    var hideDropdown = function () {
        dropdown.classList.remove('show');
        dropdown.innerHTML = '';
    };

    var render = function (rows) {
        if (!rows.length) {
            dropdown.innerHTML = '<div class="p-3 small text-muted text-center">No patients found</div>';
            dropdown.classList.add('show');
            return;
        }
        dropdown.innerHTML = rows.map(function (row) {
            return '<a href="' + row.profile_url + '" class="topbar-search-item">' +
                '<div class="topbar-search-item-name">' + row.name + '</div>' +
                '<div class="topbar-search-item-sub">' + row.phone + (row.subtitle ? ' · ' + row.subtitle : '') + '</div></a>';
        }).join('');
        dropdown.classList.add('show');
    };

    input.addEventListener('input', function () {
        clearTimeout(timer);
        var q = input.value.trim();
        if (q.length < 2) { hideDropdown(); return; }
        timer = setTimeout(function () {
            fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            })
                .then(function (r) { return r.json(); })
                .then(function (data) { render(data.results || []); })
                .catch(hideDropdown);
        }, 280);
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            hideDropdown();
        }
    });
})();
</script>
@endif

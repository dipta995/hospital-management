@php
    $userGuard = Auth::guard('admin')->user();
@endphp
<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header topbar-navbar">
            <div class="topbar-item topbar-menu-wrap flex-shrink-0">
                <button type="button" class="button-toggle-menu topbar-button topbar-menu-btn" aria-label="{{ t('common.open_menu') }}">
                    <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                </button>
            </div>

            <div class="topbar-left d-flex align-items-center">
                @if($userGuard->can('users.index'))
                <div class="topbar-patient-search d-none d-md-block">
                    <div class="topbar-search-wrap">
                        <i class="fas fa-search topbar-search-icon"></i>
                        <input type="search" id="global-patient-search" class="topbar-search-input"
                               placeholder="{{ t('common.search_patient') }}" autocomplete="off">
                        <div id="global-patient-search-results" class="topbar-search-dropdown"></div>
                    </div>
                </div>
                @endif
            </div>

            <div class="topbar-right d-flex align-items-center">
                <span class="badge bg-danger topbar-sms-badge">
                    {{ \App\Models\SmsBalance::where('branch_id',auth()->user()->branch_id)->first()->balance ?? 0 }} {{ t('common.points') }}
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
                                class="visually-hidden">{{ t('common.unread_messages') }}</span></span>
                    </button>
                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end"
                         aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold">{{ t('common.notifications') }}</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                        <small>{{ t('common.clear_all') }}</small>
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
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm">{{ t('common.view_all_notifications') }} <i
                                    class="bx bx-right-arrow-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>

                @if ( Auth::guard('admin')->user()->can('reports.index'))
                    <button id="openBalanceModal" class="btn btn-info btn-sm" title="{{ t('common.balance') }}">
                        <i class="fas fa-eye"></i>
                    </button>
                @endif
                <!-- Modal Structure -->
                <div id="balanceModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ t('common.current_month_summary') }}</h5>
                                <button type="button" class="btn-close close-modal" aria-label="{{ t('common.close') }}"></button>
                            </div>
                            <div class="modal-body">
                                {!! currentBalanceMonth() !!}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary close-modal">{{ t('common.close') }}</button>
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

                <div class="topbar-user-chip" title="{{ t('common.logged_in_as') }} {{ $userGuard->name }}">
                    <i class="fas fa-user-shield"></i>
                    <div class="topbar-user-text">
                        <span class="topbar-user-label">{{ t('common.logged_in_as') }}</span>
                        <strong class="topbar-user-name">{{ $userGuard->name }}</strong>
                    </div>
                </div>

                <a href="{{ route('admin.help.index') }}" class="btn btn-outline-secondary btn-sm me-2" title="{{ t('help.menu') }}">
                    <i class="fas fa-circle-question"></i>
                    <span class="d-none d-xl-inline ms-1">{{ t('help.menu') }}</span>
                </a>
                <a href="{{ route('admin.logout.submit') }}" class="btn btn-outline-danger btn-sm topbar-logout-btn" title="{{ t('common.logout') }}">
                    <i class="bx bx-log-out"></i>
                    <span class="d-none d-xl-inline">{{ t('common.logout') }}</span>
                </a>
            </div>
        </div>

        @include('backend.layouts.partials.topbar-quick-links')
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
        justify-content: flex-end;
    }

    .topbar-patient-search {
        flex: 0 1 360px;
        min-width: 200px;
        max-width: 420px;
        width: 100%;
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

    .tb-quick-bar {
        display: flex;
        align-items: stretch;
        gap: 4px;
        width: 100%;
        border-top: 1px solid #e8edf3;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        padding: 8px 4px 10px;
        position: relative;
    }

    .tb-quick-bar::before,
    .tb-quick-bar::after {
        content: '';
        position: absolute;
        top: 8px;
        bottom: 10px;
        width: 28px;
        pointer-events: none;
        z-index: 2;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .tb-quick-bar::before {
        left: 36px;
        background: linear-gradient(90deg, #f8fafc 30%, transparent);
    }

    .tb-quick-bar::after {
        right: 36px;
        background: linear-gradient(270deg, #f8fafc 30%, transparent);
    }

    .tb-quick-bar.can-scroll-left::before,
    .tb-quick-bar.can-scroll-right::after {
        opacity: 1;
    }

    .tb-quick-bar-track {
        flex: 1 1 auto;
        min-width: 0;
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
        -webkit-overflow-scrolling: touch;
    }

    .tb-quick-bar-track::-webkit-scrollbar {
        height: 5px;
    }

    .tb-quick-bar-track::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .tb-quick-bar-inner {
        display: flex;
        align-items: center;
        gap: 8px;
        width: max-content;
        min-width: 100%;
        padding: 2px 6px;
    }

    .tb-quick-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: transform 0.12s ease, box-shadow 0.12s ease, border-color 0.12s ease;
    }

    .tb-quick-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        color: inherit;
    }

    .tb-quick-item-icon {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        flex-shrink: 0;
    }

    .tb-quick-item--patients .tb-quick-item-icon { background: #334155; color: #fff; }
    .tb-quick-item--invoices .tb-quick-item-icon { background: #059669; color: #fff; }
    .tb-quick-item--admits .tb-quick-item-icon { background: #2563eb; color: #fff; }
    .tb-quick-item--recepts .tb-quick-item-icon { background: #f59e0b; color: #fff; }
    .tb-quick-item--lab .tb-quick-item-icon { background: #4f46e5; color: #fff; }
    .tb-quick-item--cost .tb-quick-item-icon { background: #7e22ce; color: #fff; }

    .tb-quick-item.is-active {
        border-color: #93c5fd;
        background: #eff6ff;
        color: #1d4ed8;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    .tb-quick-scroll {
        flex: 0 0 32px;
        width: 32px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        align-self: center;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .tb-quick-scroll:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    .tb-quick-scroll[hidden] {
        display: none !important;
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
        .tb-quick-item {
            font-size: 0.76rem;
            padding: 7px 12px;
        }

        .topbar-user-chip {
            max-width: 160px;
        }
    }

    @media (max-width: 1199.98px) {
        .tb-quick-item-label {
            max-width: 88px;
            overflow: hidden;
            text-overflow: ellipsis;
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
            justify-content: stretch;
        }

        .topbar-patient-search {
            flex: 1 1 auto;
            max-width: none;
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
            <h5 class="text-white m-0">{{ t('common.theme_settings') }}</h5>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas"
                    aria-label="{{ t('common.close') }}"></button>
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
            dropdown.innerHTML = '<div class="p-3 small text-muted text-center">' + @json(t('common.no_patients_found')) + '</div>';
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

<script>
(function () {
    document.querySelectorAll('[data-tb-quick-bar]').forEach(function (bar) {
        var track = bar.querySelector('[data-tb-quick-track]');
        var prev = bar.querySelector('[data-tb-quick-prev]');
        var next = bar.querySelector('[data-tb-quick-next]');
        if (!track) return;

        var update = function () {
            var maxScroll = track.scrollWidth - track.clientWidth;
            var canScroll = maxScroll > 4;
            var atStart = track.scrollLeft <= 4;
            var atEnd = track.scrollLeft >= maxScroll - 4;

            bar.classList.toggle('can-scroll-left', canScroll && !atStart);
            bar.classList.toggle('can-scroll-right', canScroll && !atEnd);

            if (prev) prev.hidden = !canScroll || atStart;
            if (next) next.hidden = !canScroll || atEnd;
        };

        var scrollBy = function (dir) {
            track.scrollBy({ left: dir * Math.max(180, track.clientWidth * 0.55), behavior: 'smooth' });
        };

        prev?.addEventListener('click', function () { scrollBy(-1); });
        next?.addEventListener('click', function () { scrollBy(1); });
        track.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    });
})();
</script>

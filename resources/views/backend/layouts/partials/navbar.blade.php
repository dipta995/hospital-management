@php
    $userGuard = Auth::guard('admin')->user();
@endphp
<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center gap-5">
                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu topbar-button">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Quick Actions moved to popup -->
                @if ( $userGuard->can('invoices.index') || $userGuard->can('invoices.create') || $userGuard->can('invoices.edit') || $userGuard->can('invoices.delete'))
                    <button type="button" id="openQuickNavModal" class="btn btn-outline-light px-3">
                        <i class="fas fa-th-large me-1"></i> Quick Menu
                    </button>
                @endif
            </div>

            <div class="d-flex align-items-center gap-1">
                <span class="badge bg-danger">

                    {{ \App\Models\SmsBalance::where('branch_id',auth()->user()->branch_id)->first()->balance ?? 0 }} Point's
                </span>
                <!-- Quick Menu & Calculator trigger (replaces fullscreen button) -->
                <div class="topbar-item d-none d-lg-flex">
                    <button type="button" class="topbar-button" id="openQuickNavModalIcon" title="Quick Menu">
                        <iconify-icon icon="solar:menu-dots-broken" class="fs-24 align-middle"></iconify-icon>
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
                    <!-- Trigger Button -->
                    <button id="openBalanceModal" class="btn btn-info">
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

                <!-- User -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                                        <span class="d-flex align-items-center">
                                             <img class="rounded-circle" width="32"
                                                  src="{{ asset('backend/assets/images/users/avatar-1.jpg') }}"
                                                  alt="avatar-3">
                                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}!</h6>


                        <div class="dropdown-divider my-1"></div>

                        <a class="dropdown-item text-danger" href="{{ route('admin.logout.submit') }}">
                            <i class="bx bx-log-out fs-18 align-middle me-1"></i><span
                                class="align-middle">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Quick Menu & Calculator Modal -->
<div id="quickNavModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Menu & Calculator</h5>
                <button type="button" class="btn-close close-quicknav" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="mb-3">Shortcuts</h6>
                        <div class="d-grid gap-2">
                            @if ( $userGuard->can('invoices.index'))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-dark btn-sm text-start">
                                    <i class="fas fa-users me-1"></i> Patient List
                                </a>
                                <a href="{{ route('admin.invoices.index') }}" class="btn btn-success btn-sm text-start">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Invoice List
                                </a>
                                <a href="{{ route('admin.admits.index') }}" class="btn btn-secondary btn-sm text-start">
                                    <i class="fas fa-procedures me-1"></i> Admit List
                                </a>
                                <a href="{{ route('admin.recepts.index') }}" class="btn btn-warning btn-sm text-start">
                                    <i class="fas fa-receipt me-1"></i> Recept List
                                </a>
                            @endif

                            @if ( $userGuard->can('labs.index'))
                                <a href="{{ route('admin.labs.index') }}" class="btn btn-primary btn-sm text-start">
                                    <i class="fas fa-vials me-1"></i> My Lab
                                </a>
                            @endif

                            @if ( $userGuard->can('costs.create'))
                                <a href="{{ route('admin.costs.create') }}" class="btn btn-dark btn-sm text-start">
                                    <i class="fas fa-money-bill-wave me-1"></i> Add Cost's
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Calculator</h6>
                        <div class="quick-calc">
                            <input type="text" id="quickCalcDisplay" class="quick-calc-display form-control mb-2" value="0" readonly>
                            <div class="quick-calc-grid">
                                <button type="button" class="quick-calc-btn" data-value="C">C</button>
                                <button type="button" class="quick-calc-btn" data-value="DEL">⌫</button>
                                <button type="button" class="quick-calc-btn" data-value="/">÷</button>
                                <button type="button" class="quick-calc-btn" data-value="*">×</button>

                                <button type="button" class="quick-calc-btn" data-value="7">7</button>
                                <button type="button" class="quick-calc-btn" data-value="8">8</button>
                                <button type="button" class="quick-calc-btn" data-value="9">9</button>
                                <button type="button" class="quick-calc-btn" data-value="-">−</button>

                                <button type="button" class="quick-calc-btn" data-value="4">4</button>
                                <button type="button" class="quick-calc-btn" data-value="5">5</button>
                                <button type="button" class="quick-calc-btn" data-value="6">6</button>
                                <button type="button" class="quick-calc-btn" data-value="+">+</button>

                                <button type="button" class="quick-calc-btn" data-value="1">1</button>
                                <button type="button" class="quick-calc-btn" data-value="2">2</button>
                                <button type="button" class="quick-calc-btn" data-value="3">3</button>
                                <button type="button" class="quick-calc-btn quick-calc-btn-eq" data-value="=">=</button>

                                <button type="button" class="quick-calc-btn quick-calc-btn-zero" data-value="0">0</button>
                                <button type="button" class="quick-calc-btn" data-value=".">.</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-quicknav">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
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

        // Quick menu modal
        function openQuickNav() {
            $('#quickNavModal').fadeIn().addClass('show').css('display', 'block');
        }

        function closeQuickNav() {
            $('#quickNavModal').fadeOut().removeClass('show').css('display', 'none');
        }

        $('#openQuickNavModal, #openQuickNavModalIcon').on('click', function () {
            openQuickNav();
        });

        $('.close-quicknav').on('click', function () {
            closeQuickNav();
        });

        $('#quickNavModal').on('click', function (e) {
            if ($(e.target).is('#quickNavModal')) {
                closeQuickNav();
            }
        });

        // Calculator logic
        let calcExpression = '';
        const $calcDisplay = $('#quickCalcDisplay');

        function updateCalcDisplay() {
            $calcDisplay.val(calcExpression || '0');
        }

        $('.quick-calc-btn').on('click', function () {
            const value = $(this).data('value');

            if (value === 'C') {
                calcExpression = '';
            } else if (value === 'DEL') {
                calcExpression = calcExpression.slice(0, -1);
            } else if (value === '=') {
                if (!calcExpression) {
                    return;
                }
                try {
                    // Allow only safe characters
                    const safeExpr = calcExpression.replace(/[^0-9+\-*/.()]/g, '');
                    // eslint-disable-next-line no-eval
                    const result = eval(safeExpr);
                    calcExpression = (result !== undefined && result !== null) ? result.toString() : '';
                } catch (e) {
                    calcExpression = '';
                    $calcDisplay.val('Error');
                    return;
                }
            } else {
                calcExpression += value.toString();
            }

            updateCalcDisplay();
        });

        updateCalcDisplay();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.querySelector('.button-toggle-menu.topbar-button');

        if (toggleBtn) {
            toggleBtn.addEventListener("click", function () {
                document.documentElement.classList.toggle("sidebar-enable");
            });
        }
    });
</script>

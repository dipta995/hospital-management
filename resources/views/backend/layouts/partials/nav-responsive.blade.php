<style>
    /* ── Mobile sidebar overlay ── */
    html.sidebar-enable[data-menu-size=hidden] .main-nav,
    html[data-menu-size=hidden].sidebar-enable .main-nav,
    html.sidebar-enable .main-nav,
    html.sidebar-enable .main-nav {
        margin-left: 0 !important;
        z-index: 1055 !important;
    }

    html.sidebar-enable .offcanvas-backdrop,
    body.sidebar-enable .offcanvas-backdrop {
        z-index: 1050;
    }

    @media (max-width: 991.98px) {
        /* Theme adds sidebar width as topbar padding — pushes menu off-screen on phones */
        .topbar {
            padding-left: 0.65rem !important;
            padding-right: 0.65rem !important;
            height: auto;
            min-height: 56px;
        }

        .page-content {
            margin-left: 0 !important;
        }

        .main-nav {
            position: fixed !important;
            left: 0;
            top: 0;
            bottom: 0;
            margin-left: 0 !important;
            transform: translateX(-110%);
            transition: transform 0.25s ease;
            z-index: 1055 !important;
        }

        html.sidebar-enable .main-nav {
            transform: translateX(0);
            box-shadow: 8px 0 32px rgba(15, 23, 42, 0.18);
        }

        .button-toggle-menu,
        .topbar-menu-wrap {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .topbar-menu-wrap {
            flex: 0 0 auto;
            grid-column: 1;
            grid-row: 1;
        }

        .topbar-left {
            grid-column: 2;
            grid-row: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .topbar-right {
            grid-column: 3;
            grid-row: 1;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            flex-wrap: nowrap;
            min-width: 0;
            margin-left: 0;
            padding-left: 0;
        }

        .topbar-navbar {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 8px;
            width: 100%;
            min-height: 56px;
        }

        .topbar-menu-btn {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0c4a6e, #0369a1);
            border: none;
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(3, 105, 161, 0.35);
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        .topbar-menu-btn:hover {
            background: linear-gradient(135deg, #075985, #0284c7);
            color: #fff !important;
        }

        .topbar-menu-btn iconify-icon {
            color: #fff;
        }

        .topbar-item .topbar-button:not(.topbar-menu-btn) {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .topbar-item .topbar-button .topbar-badge {
            top: 4px;
            right: 2px;
            transform: none;
        }

        .topbar-user-chip {
            display: none !important;
        }

        #openBalanceModal {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .topbar-logout-btn {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .topbar-sms-badge {
            display: none !important;
        }
    }

    /* ── Topbar mobile layout ── */
    @media (max-width: 991.98px) {
        .topbar {
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .topbar .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }

        .page-content {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }

    @media (max-width: 575.98px) {
        .topbar {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .topbar-navbar {
            min-height: 52px;
            gap: 6px;
        }

        .tb-quick-chip {
            min-width: 62px;
            padding: 7px 8px;
        }
    }
</style>

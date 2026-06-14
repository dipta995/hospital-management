<style>
    /* ── Mobile sidebar: theme CSS uses html.sidebar-enable but app.js toggles body ── */
    html.sidebar-enable[data-menu-size=hidden] .main-nav,
    html[data-menu-size=hidden].sidebar-enable .main-nav {
        margin-left: 0 !important;
        z-index: 1055 !important;
    }

    html.sidebar-enable[data-menu-size=hidden] .offcanvas-backdrop,
    body.sidebar-enable .offcanvas-backdrop {
        z-index: 1050;
    }

    /* ── Topbar mobile layout ── */
    @media (max-width: 991.98px) {
        .topbar {
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .topbar .container-fluid {
            padding-left: 0.65rem;
            padding-right: 0.65rem;
        }

        .topbar-navbar {
            flex-wrap: nowrap;
            min-height: 56px;
            gap: 6px;
        }

        .topbar-left {
            flex: 1 1 auto;
            min-width: 0;
            overflow: hidden;
        }

        .topbar-right {
            flex: 0 0 auto;
            width: auto;
            padding-top: 0;
            padding-left: 0;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .topbar-quick-links {
            display: none !important;
        }

        .topbar-mobile-quick {
            display: inline-flex !important;
        }

        .topbar-sms-badge {
            font-size: 0.68rem;
            padding: 0.25rem 0.45rem;
        }

        .topbar-user-chip {
            padding: 4px 8px;
            max-width: none;
        }

        .topbar-user-text {
            display: none;
        }

        .topbar-user-chip > i {
            font-size: 1rem;
        }

        #openBalanceModal {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
        }

        .topbar-logout-btn span {
            display: none !important;
        }

        .topbar-logout-btn {
            padding: 0.3rem 0.55rem;
        }

        .topbar-item .topbar-button {
            padding: 0.25rem;
        }

        .page-content {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }

    @media (max-width: 575.98px) {
        .topbar-sms-badge {
            display: none;
        }

        .topbar-navbar {
            min-height: 52px;
        }
    }

    @media (min-width: 992px) {
        .topbar-mobile-quick {
            display: none !important;
        }
    }

    .topbar-mobile-quick .dropdown-menu {
        max-height: 70vh;
        overflow-y: auto;
        min-width: 220px;
        border-radius: 12px;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.15);
    }

    .topbar-mobile-quick .dropdown-item {
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.55rem 1rem;
    }

    .topbar-mobile-quick .dropdown-toggle {
        white-space: nowrap;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.35rem 0.65rem;
    }
</style>

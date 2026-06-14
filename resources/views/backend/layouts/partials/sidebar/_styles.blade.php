<style>
    /* ── Sidebar section grouping ── */
    .main-nav .menu-title {
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        padding: 14px 20px 6px;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .main-nav .menu-title::before {
        content: '';
        width: 3px;
        height: 14px;
        border-radius: 2px;
        background: #94a3b8;
        flex-shrink: 0;
    }

    .main-nav .menu-title.section-main::before { background: #0369a1; }
    .main-nav .menu-title.section-patients::before { background: #0891b2; }
    .main-nav .menu-title.section-diagnostic::before { background: #7c3aed; }
    .main-nav .menu-title.section-doctor::before { background: #059669; }
    .main-nav .menu-title.section-hospital::before { background: #dc2626; }
    .main-nav .menu-title.section-pharmacy::before { background: #9333ea; }
    .main-nav .menu-title.section-finance::before { background: #d97706; }
    .main-nav .menu-title.section-hr::before { background: #2563eb; }
    .main-nav .menu-title.section-inventory::before { background: #0d9488; }
    .main-nav .menu-title.section-reports::before { background: #4f46e5; }
    .main-nav .menu-title.section-admin::before { background: #475569; }
    .main-nav .menu-title.section-tools::before { background: #78716c; }

    .main-nav .menu-title i {
        font-size: 0.72rem;
        opacity: 0.85;
    }

    /* Module mega-menu (Pharmacy, Hospital, etc.) */
    .sidebar-module > .nav-link {
        font-weight: 600;
        border-radius: 8px;
        margin: 2px 10px;
        width: calc(100% - 20px);
    }

    .sidebar-module-pharmacy > .nav-link { background: rgba(147, 51, 234, 0.08); }
    .sidebar-module-pharmacy > .nav-link.active,
    .sidebar-module-pharmacy > .nav-link:not(.collapsed) { background: rgba(147, 51, 234, 0.15); color: #7e22ce; }

    .sidebar-module-diagnostic > .nav-link { background: rgba(124, 58, 237, 0.08); }
    .sidebar-module-diagnostic > .nav-link.active,
    .sidebar-module-diagnostic > .nav-link:not(.collapsed) { background: rgba(124, 58, 237, 0.15); color: #6d28d9; }

    .sidebar-module-doctor > .nav-link { background: rgba(5, 150, 105, 0.08); }
    .sidebar-module-doctor > .nav-link.active,
    .sidebar-module-doctor > .nav-link:not(.collapsed) { background: rgba(5, 150, 105, 0.15); color: #047857; }

    .sidebar-module-hospital > .nav-link { background: rgba(220, 38, 38, 0.07); }
    .sidebar-module-hospital > .nav-link.active,
    .sidebar-module-hospital > .nav-link:not(.collapsed) { background: rgba(220, 38, 38, 0.12); color: #b91c1c; }

    .sidebar-module-inventory > .nav-link { background: rgba(13, 148, 136, 0.08); }
    .sidebar-module-inventory > .nav-link.active,
    .sidebar-module-inventory > .nav-link:not(.collapsed) { background: rgba(13, 148, 136, 0.14); color: #0f766e; }

    .sidebar-module-finance > .nav-link { background: rgba(217, 119, 6, 0.08); }
    .sidebar-module-finance > .nav-link.active,
    .sidebar-module-finance > .nav-link:not(.collapsed) { background: rgba(217, 119, 6, 0.14); color: #b45309; }

    .sidebar-module-hr > .nav-link { background: rgba(37, 99, 235, 0.08); }
    .sidebar-module-hr > .nav-link.active,
    .sidebar-module-hr > .nav-link:not(.collapsed) { background: rgba(37, 99, 235, 0.14); color: #1d4ed8; }

    /* Sub-section labels inside mega-menus */
    .sub-nav-section {
        padding: 10px 16px 4px 20px;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        list-style: none;
    }

    .sidebar-module-inner .sub-nav-link {
        padding-left: 24px;
        font-size: 0.88rem;
    }

    .sidebar-module-inner .sub-nav-item + .sub-nav-section {
        margin-top: 6px;
        padding-top: 12px;
        border-top: 1px dashed rgba(148, 163, 184, 0.35);
    }

    /* Search box polish */
    .sidebar-search-item .input-group {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }

    .sidebar-search-item .form-control {
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
    }

    .menu-title.sidebar-hidden {
        display: none !important;
    }
</style>

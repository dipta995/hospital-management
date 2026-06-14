<style>
    .pharm-page {
        --pharm-primary: #6d28d9;
        --pharm-primary-dark: #5b21b6;
        --pharm-accent: #8b5cf6;
        --pharm-success: #059669;
        --pharm-warning: #d97706;
        --pharm-danger: #dc2626;
        --pharm-border: #e2e8f0;
        --pharm-muted: #64748b;
        --pharm-surface: #ffffff;
    }

    .pharm-page .crud-hero {
        background: linear-gradient(135deg, var(--pharm-primary) 0%, var(--pharm-accent) 100%);
        box-shadow: 0 10px 28px rgba(109, 40, 217, 0.2);
    }

    .pharm-page .btn-crud-light:hover { color: var(--pharm-primary); }
    .pharm-page .btn-crud-primary { color: var(--pharm-primary); }
    .pharm-page .btn-crud-primary:hover { color: var(--pharm-primary-dark); }
    .pharm-page .btn-crud-submit {
        background: linear-gradient(135deg, var(--pharm-primary), var(--pharm-accent));
    }
    .pharm-page .btn-crud-submit:hover {
        background: linear-gradient(135deg, var(--pharm-primary-dark), #7c3aed);
    }
    .pharm-page .crud-badge.pharm { background: #f5f3ff; color: #6d28d9; border-color: rgba(109,40,217,0.15); }

    .pharm-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .pharm-kpi {
        background: var(--pharm-surface);
        border: 1px solid var(--pharm-border);
        border-radius: 14px;
        padding: 16px 18px;
    }

    .pharm-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--pharm-muted);
        margin-bottom: 4px;
    }

    .pharm-kpi-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
    }

    .pharm-stock-ok { background: #ecfdf5; color: #047857; }
    .pharm-stock-low { background: #fffbeb; color: #b45309; }
    .pharm-stock-out { background: #fef2f2; color: #b91c1c; }

    /* POS layout */
    .pharm-pos-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 1100px) {
        .pharm-pos-grid { grid-template-columns: 1fr; }
    }

    .pharm-pos-panel {
        background: #fff;
        border: 1px solid var(--pharm-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .pharm-pos-panel-head {
        background: #faf5ff;
        border-bottom: 1px solid var(--pharm-border);
        padding: 12px 16px;
        font-weight: 700;
        color: #5b21b6;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pharm-pos-panel-body { padding: 16px; }

    .pharm-cart-table thead th {
        background: #f8fafc;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .pharm-summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.92rem;
    }

    .pharm-summary-row.total {
        font-size: 1.1rem;
        font-weight: 800;
        color: #5b21b6;
        border-bottom: none;
        padding-top: 12px;
    }

    .pharm-summary-box {
        background: #faf5ff;
        border: 1px solid #ede9fe;
        border-radius: 12px;
        padding: 12px 14px;
    }

    .pharm-summary-sticky {
        position: sticky;
        top: 80px;
    }

    .pharm-table td, .pharm-cart-table td {
        vertical-align: middle;
    }

    .pharm-cart-table .select2-container {
        width: 100% !important;
    }

    /* Purchase form */
    .pharm-purchase-grid {
        grid-template-columns: 1fr 340px;
    }

    @media (max-width: 1200px) {
        .pharm-purchase-grid { grid-template-columns: 1fr; }
        .pharm-purchase-summary { position: static !important; }
    }

    .pharm-purchase-table thead th {
        white-space: nowrap;
        padding: 10px 12px;
        color: #64748b;
        font-weight: 700;
        border-bottom: 2px solid #ede9fe;
    }

    .pharm-purchase-table tbody td {
        padding: 10px 12px;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    .pharm-purchase-table .form-control-sm {
        border-radius: 8px;
        min-width: 70px;
    }

    .pharm-col-qty { width: 80px; }
    .pharm-col-price { width: 100px; }
    .pharm-col-disc { width: 90px; }
    .pharm-col-total { width: 100px; white-space: nowrap; }
    .pharm-col-expiry { width: 140px; }
    .pharm-col-action { width: 48px; }

    .pharm-cart-product strong {
        color: #1e293b;
        font-size: 0.92rem;
    }

    .pharm-purchase-empty {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }

    .pharm-purchase-empty i {
        font-size: 2.5rem;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .pharm-purchase-empty p {
        margin-bottom: 4px;
        font-weight: 600;
        color: #64748b;
    }

    .pharm-due-display {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1.25rem;
        font-weight: 800;
        color: #b91c1c;
        text-align: center;
    }

    .pharm-due-display.zero-due {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .pharm-purchase-summary .pharm-pos-panel-head {
        justify-content: flex-start;
    }

    .pharm-page .select2-container--default .select2-selection--single {
        min-height: 42px;
        border-radius: 10px;
        border-color: #cbd5e1;
        display: flex;
        align-items: center;
        padding: 4px 10px;
    }

    .pharm-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }

    .pharm-page .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #7c3aed;
    }

    .pharm-pos-panel-head .badge {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 5px 10px;
    }

    .pharm-suggestion {
        position: absolute;
        z-index: 1050;
        width: 100%;
        max-height: 220px;
        overflow-y: auto;
        display: none;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        border-radius: 0 0 10px 10px;
    }

    .pharm-suggestion .list-group-item {
        cursor: pointer;
        font-size: 0.9rem;
    }

    .pharm-suggestion .list-group-item:hover {
        background: #f5f3ff;
    }

    .ui-autocomplete {
        z-index: 1060 !important;
        max-height: 240px;
        overflow-y: auto;
        border-radius: 10px;
        border-color: #cbd5e1;
    }

    .ui-menu-item-wrapper.ui-state-active {
        background: #8b5cf6 !important;
        border-color: #8b5cf6 !important;
    }
</style>

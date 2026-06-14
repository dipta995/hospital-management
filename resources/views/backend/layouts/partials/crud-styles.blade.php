<style>
    .crud-page {
        --crud-primary: #0f766e;
        --crud-primary-dark: #0b5f58;
        --crud-accent: #14b8a6;
        --crud-border: #e2e8f0;
        --crud-muted: #64748b;
        --crud-bg: #f8fafc;
    }

    .crud-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        background: linear-gradient(135deg, var(--crud-primary) 0%, var(--crud-accent) 100%);
        color: #fff;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 18px;
        box-shadow: 0 10px 28px rgba(15, 118, 110, 0.18);
    }

    .crud-hero-content {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .crud-hero-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .crud-hero-title {
        margin: 0;
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .crud-hero-subtitle {
        margin: 4px 0 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.92rem;
    }

    .crud-hero-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-crud-light {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.28);
        color: #fff;
        border-radius: 10px;
        padding: 0.55rem 1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
    }

    .btn-crud-light:hover {
        background: #fff;
        color: var(--crud-primary);
    }

    .btn-crud-primary {
        background: #fff;
        border: none;
        color: var(--crud-primary);
        border-radius: 10px;
        padding: 0.55rem 1rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
    }

    .btn-crud-primary:hover {
        color: var(--crud-primary-dark);
        transform: translateY(-1px);
    }

    .crud-card {
        background: #fff;
        border: 1px solid var(--crud-border);
        border-radius: 14px;
        padding: 20px 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        margin-bottom: 18px;
    }

    .crud-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        padding: 14px 16px;
        background: var(--crud-bg);
        border: 1px solid var(--crud-border);
        border-radius: 12px;
    }

    .crud-toolbar .form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
    }

    .crud-table-wrap {
        border: 1px solid var(--crud-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .crud-table {
        margin-bottom: 0;
    }

    .crud-table thead th {
        background: #f1f5f9;
        color: #334155;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        font-weight: 700;
        border-bottom: 1px solid var(--crud-border);
        white-space: nowrap;
    }

    .crud-table tbody td {
        vertical-align: middle;
        color: #1e293b;
    }

    .crud-table tbody tr:hover {
        background: #f8fafc;
    }

    .crud-action-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .crud-btn-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        text-decoration: none;
        color: #fff;
        transition: transform 0.12s ease, opacity 0.12s ease;
    }

    .crud-btn-icon:hover {
        color: #fff;
        transform: translateY(-1px);
        opacity: 0.92;
    }

    .crud-btn-edit { background: #0ea5e9; }
    .crud-btn-delete { background: #ef4444; }
    .crud-btn-view { background: #10b981; }
    .crud-btn-info { background: #6366f1; }
    .crud-btn-warning { background: #f59e0b; color: #1e293b; }
    .crud-btn-dark { background: #334155; }

    .crud-empty {
        text-align: center;
        padding: 28px 16px;
        color: var(--crud-muted);
    }

    .crud-form-section {
        border: 1px solid var(--crud-border);
        border-radius: 12px;
        margin-bottom: 18px;
        overflow: hidden;
    }

    .crud-form-section-header {
        background: #f8fafc;
        border-bottom: 1px solid var(--crud-border);
        padding: 12px 16px;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .crud-form-section-body {
        padding: 16px;
    }

    .crud-form-grid .form-label,
    .crud-form-grid label {
        font-size: 0.88rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }

    .crud-form-grid .form-control,
    .crud-form-grid .form-select {
        border-radius: 10px;
        border-color: #cbd5e1;
        min-height: 42px;
    }

    .crud-form-grid .form-control:focus,
    .crud-form-grid .form-select:focus {
        border-color: var(--crud-accent);
        box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.15);
    }

    .crud-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 8px;
        border-top: 1px solid var(--crud-border);
        margin-top: 8px;
    }

    .btn-crud-submit {
        background: linear-gradient(135deg, var(--crud-primary), var(--crud-accent));
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 0.65rem 1.25rem;
        font-weight: 700;
    }

    .btn-crud-submit:hover {
        color: #fff;
        background: linear-gradient(135deg, var(--crud-primary-dark), #0d9488);
    }

    .btn-crud-cancel {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #475569;
        border-radius: 10px;
        padding: 0.65rem 1.25rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-crud-cancel:hover {
        background: #f8fafc;
        color: #334155;
    }

    .crud-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        background: #ecfeff;
        color: #0f766e;
        border: 1px solid rgba(15, 118, 110, 0.15);
    }

    .crud-modal .modal-content {
        border: none;
        border-radius: 14px;
        overflow: hidden;
    }

    .crud-modal .modal-header {
        background: linear-gradient(135deg, var(--crud-primary), var(--crud-accent));
        color: #fff;
        border: none;
    }

    .crud-modal .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    @media (max-width: 768px) {
        .crud-hero {
            padding: 16px;
        }

        .crud-hero-title {
            font-size: 1.2rem;
        }

        .crud-card {
            padding: 16px;
        }
    }
</style>

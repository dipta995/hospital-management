<style>
    /* ── Standard large toast (SweetAlert2) ── */
    .swal2-popup.app-toast-popup {
        min-width: 22rem;
        max-width: 28rem;
        padding: 1rem 1.15rem 0.9rem;
        font-size: 0.95rem;
        border-radius: 14px;
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.18);
        border: 1px solid #e2e8f0;
    }

    .swal2-popup.app-toast-popup .swal2-title {
        font-size: 0.98rem;
        font-weight: 700;
        line-height: 1.45;
        margin: 0.15rem 0 0;
        color: #0f172a;
    }

    .swal2-popup.app-toast-popup .swal2-icon {
        width: 2rem;
        height: 2rem;
        margin: 0 0.65rem 0 0;
        border-width: 2px;
    }

    .swal2-popup.app-toast-popup .swal2-icon .swal2-icon-content {
        font-size: 1.15rem;
    }

    .swal2-popup.app-toast-popup .swal2-timer-progress-bar {
        height: 4px;
    }

    /* ── Delete confirm dialog ── */
    .swal2-popup.app-delete-popup {
        border-radius: 16px;
        padding: 1.5rem 1.35rem 1.25rem;
        max-width: 26rem;
    }

    .swal2-popup.app-delete-popup .swal2-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
    }

    .swal2-popup.app-delete-popup .swal2-html-container {
        font-size: 0.92rem;
        color: #475569;
        line-height: 1.5;
    }

    /* ── Action tooltips (icon + badge buttons) ── */
    .has-action-tip,
    .crud-btn-icon {
        position: relative;
        overflow: visible;
    }

    .crud-tip-bubble {
        position: absolute;
        left: 50%;
        bottom: calc(100% + 14px);
        transform: translateX(-50%) scale(0.55);
        transform-origin: center bottom;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-width: 2.75rem;
        max-width: 10rem;
        padding: 9px 13px;
        background: #ffffff;
        color: #0f172a;
        font-size: 0.68rem;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: 0.01em;
        white-space: nowrap;
        border: 2px solid #94a3b8;
        border-radius: 999px;
        box-shadow:
            0 4px 10px rgba(15, 23, 42, 0.08),
            0 10px 28px rgba(15, 23, 42, 0.14);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        z-index: 120;
        transition:
            opacity 0.2s ease,
            transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1),
            visibility 0.2s ease;
    }

    .crud-tip-bubble::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -7px;
        width: 12px;
        height: 12px;
        transform: translateX(-50%) rotate(45deg);
        background: #ffffff;
        border-right: 2px solid #94a3b8;
        border-bottom: 2px solid #94a3b8;
        border-radius: 0 0 3px 0;
        box-shadow: 2px 2px 4px rgba(15, 23, 42, 0.06);
    }

    .has-action-tip:hover .crud-tip-bubble,
    .has-action-tip:focus-visible .crud-tip-bubble,
    .crud-btn-icon:hover .crud-tip-bubble,
    .crud-btn-icon:focus-visible .crud-tip-bubble {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) scale(1) translateY(-3px);
        transition-delay: 0.05s;
    }

    .crud-action-group .badge,
    .crud-action-group a.badge {
        position: relative;
        overflow: visible;
    }

    .inv-act {
        position: relative;
        overflow: visible;
    }

    .inv-act:hover .crud-tip-bubble,
    .inv-act:focus-visible .crud-tip-bubble {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) scale(1) translateY(-3px);
        transition-delay: 0.05s;
    }

    table tbody td a.badge,
    table tbody td button.badge {
        position: relative;
        overflow: visible;
    }
</style>

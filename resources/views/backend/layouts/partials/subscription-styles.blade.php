<style>
    .subscription-renewal-bar {
        padding: 0.65rem 1rem 0;
    }

    .subscription-renewal-bar__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
        max-width: 100%;
        padding: 0.55rem 0.85rem;
        border: 1px solid #f5c2c7;
        border-left: 4px solid #dc3545;
        border-radius: 0.5rem;
        background: #fff5f5;
        color: #842029;
        font-size: 0.875rem;
        line-height: 1.45;
        box-shadow: 0 1px 3px rgba(220, 53, 69, 0.08);
    }

    .subscription-renewal-bar__inner.is-expired {
        background: #dc3545;
        border-color: #b02a37;
        color: #fff;
    }

    .subscription-renewal-bar__inner.is-expired .subscription-renewal-bar__icon {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .subscription-renewal-bar__inner.is-expired .subscription-renewal-bar__days {
        color: #ffe5e7;
    }

    .subscription-renewal-bar__inner.is-expired .subscription-renewal-bar__btn {
        background: #fff;
        border-color: #fff;
        color: #b02a37;
    }

    .subscription-renewal-bar__message {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        min-width: 0;
        flex: 1 1 auto;
    }

    .subscription-renewal-bar__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 999px;
        background: #f8d7da;
        color: #b02a37;
        flex-shrink: 0;
        font-size: 0.75rem;
    }

    .subscription-renewal-bar__text strong {
        font-weight: 600;
        white-space: nowrap;
    }

    .subscription-renewal-bar__days {
        color: #a52834;
        font-size: 0.8125rem;
    }

    .subscription-renewal-bar__btn {
        flex-shrink: 0;
        background: #dc3545;
        border-color: #dc3545;
        color: #fff;
        font-weight: 600;
        padding: 0.3rem 0.85rem;
        white-space: nowrap;
    }

    .subscription-renewal-bar__btn:hover,
    .subscription-renewal-bar__btn:focus {
        background: #bb2d3b;
        border-color: #b02a37;
        color: #fff;
    }

    .subscription-expired-alert {
        border: 1px solid #f5c2c7;
        border-left: 4px solid #dc3545;
        background: #fff5f5;
        padding: 0.75rem 0.9rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .subscription-expired-alert__inner {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
    }

    .subscription-expired-alert__icon {
        color: #b02a37;
        margin-top: 0.1rem;
        flex-shrink: 0;
    }

    .subscription-expired-alert__text {
        color: #842029;
    }

    .subscription-reminder-stat {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        padding: 0.6rem 0.8rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background: #f8fafc;
    }

    .subscription-reminder-stat span {
        font-size: 0.78rem;
        color: #6b7280;
    }

    .subscription-reminder-stat strong {
        font-size: 0.98rem;
        color: #111827;
    }

    .subscription-reminder-stat.is-danger {
        border-color: #fca5a5;
        background: #fef2f2;
    }

    .subscription-reminder-stat.is-danger strong {
        color: #b91c1c;
    }

    @media (max-width: 575.98px) {
        .subscription-renewal-bar {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .subscription-renewal-bar__inner {
            flex-direction: column;
            align-items: stretch;
        }

        .subscription-renewal-bar__btn {
            width: 100%;
        }

        .subscription-renewal-bar__text strong {
            white-space: normal;
        }
    }
</style>

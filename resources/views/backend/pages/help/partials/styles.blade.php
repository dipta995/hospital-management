<style>
    :root {
        --doc-bg: #f8fafc;
        --doc-surface: #ffffff;
        --doc-border: #e2e8f0;
        --doc-text: #334155;
        --doc-heading: #0f172a;
        --doc-muted: #64748b;
        --doc-accent: #0f766e;
        --doc-accent-soft: #ecfdf5;
        --doc-sidebar-w: 280px;
    }

    .doc-page { background: var(--doc-bg); min-height: calc(100vh - 120px); }

    .doc-hero {
        background: linear-gradient(135deg, #0f172a 0%, #134e4a 55%, #0f766e 100%);
        border-radius: 18px;
        padding: 32px 36px;
        color: #fff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .doc-hero::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }

    .doc-hero h1 { font-size: 1.65rem; font-weight: 800; margin-bottom: 8px; position: relative; z-index: 1; }
    .doc-hero p { opacity: 0.88; margin: 0; max-width: 640px; position: relative; z-index: 1; }

    .doc-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .doc-search {
        flex: 1;
        min-width: 220px;
        max-width: 400px;
        position: relative;
    }

    .doc-search input {
        width: 100%;
        border: 1px solid var(--doc-border);
        border-radius: 12px;
        padding: 10px 14px 10px 40px;
        font-size: 0.9rem;
        background: var(--doc-surface);
    }

    .doc-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--doc-muted);
    }

    .doc-lang-toggle .btn { border-radius: 10px; padding: 8px 16px; font-weight: 600; }

    .doc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 16px;
    }

    .doc-module-card {
        background: var(--doc-surface);
        border: 1px solid var(--doc-border);
        border-radius: 16px;
        padding: 22px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        height: 100%;
    }

    .doc-module-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        border-color: #99f6e4;
        color: inherit;
    }

    .doc-module-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .doc-module-card h3 { font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--doc-heading); }
    .doc-module-card p { font-size: 0.85rem; color: var(--doc-muted); margin: 0; line-height: 1.5; }
    .doc-module-card .doc-arrow { margin-top: auto; font-size: 0.8rem; font-weight: 600; color: var(--doc-accent); }

    .doc-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (min-width: 1100px) {
        .doc-layout { grid-template-columns: var(--doc-sidebar-w) 1fr; }
    }

    .doc-sidebar {
        background: var(--doc-surface);
        border: 1px solid var(--doc-border);
        border-radius: 16px;
        padding: 18px;
        position: sticky;
        top: 88px;
        max-height: calc(100vh - 110px);
        overflow-y: auto;
    }

    .doc-sidebar-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        color: var(--doc-muted);
        margin-bottom: 12px;
    }

    .doc-nav-group { margin-bottom: 16px; }
    .doc-nav-group-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--doc-heading);
        margin-bottom: 6px;
        padding: 0 10px;
    }

    .doc-nav-link {
        display: block;
        padding: 8px 12px;
        border-radius: 8px;
        color: var(--doc-muted);
        text-decoration: none;
        font-size: 0.84rem;
        line-height: 1.35;
        border-left: 3px solid transparent;
        transition: all 0.12s ease;
    }

    .doc-nav-link:hover { background: #f1f5f9; color: var(--doc-heading); }
    .doc-nav-link.active {
        background: var(--doc-accent-soft);
        color: var(--doc-accent);
        border-left-color: var(--doc-accent);
        font-weight: 600;
    }

    .doc-main { min-width: 0; }

    .doc-article {
        background: var(--doc-surface);
        border: 1px solid var(--doc-border);
        border-radius: 16px;
        overflow: hidden;
    }

    .doc-article-header {
        padding: 28px 32px 20px;
        border-bottom: 1px solid var(--doc-border);
        background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
    }

    .doc-breadcrumb {
        font-size: 0.8rem;
        color: var(--doc-muted);
        margin-bottom: 12px;
    }

    .doc-breadcrumb a { color: var(--doc-accent); text-decoration: none; }

    .doc-article-header h1 {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--doc-heading);
        margin: 0 0 8px;
    }

    .doc-article-header .lead {
        color: var(--doc-muted);
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .doc-article-body { padding: 28px 32px 36px; }

    .doc-section {
        margin-bottom: 36px;
        scroll-margin-top: 100px;
    }

    .doc-section:last-child { margin-bottom: 0; }

    .doc-section h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--doc-heading);
        margin: 0 0 14px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
    }

    .doc-section > p {
        color: var(--doc-text);
        line-height: 1.75;
        margin-bottom: 16px;
        font-size: 0.92rem;
    }

    .doc-steps { list-style: none; padding: 0; margin: 0 0 16px; counter-reset: docstep; }

    .doc-step {
        display: flex;
        gap: 14px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid var(--doc-border);
        border-radius: 12px;
        margin-bottom: 10px;
        counter-increment: docstep;
    }

    .doc-step::before {
        content: counter(docstep);
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        background: var(--doc-heading);
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .doc-step-text { font-size: 0.9rem; line-height: 1.6; color: var(--doc-text); padding-top: 3px; }

    .doc-callout {
        border-radius: 12px;
        padding: 14px 16px;
        margin: 16px 0;
        font-size: 0.88rem;
        line-height: 1.55;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .doc-callout i { margin-top: 2px; flex-shrink: 0; }
    .doc-callout-tip { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    .doc-callout-warn { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .doc-callout-info { background: var(--doc-accent-soft); border: 1px solid #99f6e4; color: #115e59; }

    .doc-field-table {
        width: 100%;
        font-size: 0.88rem;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--doc-border);
        border-radius: 12px;
        overflow: hidden;
        margin: 16px 0;
    }

    .doc-field-table th {
        background: #f8fafc;
        padding: 10px 14px;
        font-weight: 700;
        color: var(--doc-heading);
        border-bottom: 1px solid var(--doc-border);
    }

    .doc-field-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--doc-text);
        vertical-align: top;
    }

    .doc-field-table tr:last-child td { border-bottom: none; }

    .doc-flow {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin: 16px 0;
    }

    .doc-flow-item {
        background: #f1f5f9;
        border: 1px solid var(--doc-border);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--doc-heading);
    }

    .doc-flow-arrow { color: var(--doc-muted); font-size: 0.75rem; }

    .doc-mobile-nav {
        display: block;
        margin-bottom: 16px;
    }

    @media (min-width: 1100px) {
        .doc-mobile-nav { display: none; }
    }

    .doc-faq-item {
        border: 1px solid var(--doc-border);
        border-radius: 12px;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .doc-faq-q {
        padding: 14px 16px;
        font-weight: 700;
        font-size: 0.9rem;
        background: #f8fafc;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .doc-faq-a {
        padding: 0 16px 14px;
        font-size: 0.88rem;
        color: var(--doc-text);
        line-height: 1.6;
        display: none;
    }

    .doc-faq-item.open .doc-faq-a { display: block; padding-top: 0; }
</style>

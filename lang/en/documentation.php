<?php

return [
    'labels' => [
        'field' => 'Field',
        'description' => 'Description',
        'faq' => 'Common questions',
        'all_modules' => 'All modules',
        'read_guide' => 'Read guide',
        'search_placeholder' => 'Search modules…',
        'on_this_page' => 'On this page',
        'full_guide' => 'Complete guide',
    ],

    'meta' => [
        'title' => 'Documentation',
        'subtitle' => 'Learn how each module works — step by step, with field explanations and workflows.',
        'guide_title' => 'Hospital Management System',
    ],

    'nav_groups' => [
        'start' => 'Getting started',
        'clinical' => 'Clinical & patients',
        'business' => 'Operations & finance',
        'system' => 'System & support',
    ],

    'modules' => [

        'getting_started' => [
            'group' => 'start',
            'icon' => 'fa-sign-in-alt',
            'color' => '#0f172a',
            'bg' => '#f1f5f9',
            'title' => 'Getting Started',
            'summary' => 'Login, password, language, roles, and first-day checklist.',
            'intro' => 'This section explains how to access the system and what you need before using any module.',
            'sections' => [
                [
                    'id' => 'login',
                    'title' => 'How to log in',
                    'steps' => [
                        'Open your hospital URL and add `/admin/login` — example: `https://hospital.com/admin/login`',
                        'Enter the email and password given by your Super Admin.',
                        'Click Login. You will arrive at the Dashboard.',
                        'If subscription expired, a payment banner may block access — contact admin.',
                    ],
                    'tips' => [
                        'Default Super Admin after fresh install: `superadmin@email.com` / `12344321` — change immediately.',
                    ],
                ],
                [
                    'id' => 'password',
                    'title' => 'Change password',
                    'steps' => [
                        'From sidebar: Main → Update Password.',
                        'Enter current password, new password, and confirmation.',
                        'Save. Log in again if session expires.',
                    ],
                ],
                [
                    'id' => 'language',
                    'title' => 'Switch language',
                    'steps' => [
                        'Use the language switch in the top navbar (English / বাংলা).',
                        'The interface reloads in the selected language.',
                        'Documentation is also available in both languages.',
                    ],
                ],
                [
                    'id' => 'roles',
                    'title' => 'How roles & permissions work',
                    'intro' => 'Every staff account has a Role. Each role controls which menus and actions are allowed.',
                    'fields' => [
                        ['name' => 'view', 'desc' => 'See list and details'],
                        ['name' => 'create', 'desc' => 'Add new records'],
                        ['name' => 'edit', 'desc' => 'Modify existing records'],
                        ['name' => 'delete', 'desc' => 'Remove records'],
                    ],
                    'steps' => [
                        'If a menu is missing, ask Super Admin to open Administration → Roles.',
                        'Edit your role and enable the required module permissions.',
                        'Logout and login again to refresh menu.',
                    ],
                ],
            ],
        ],

        'dashboard' => [
            'group' => 'start',
            'icon' => 'fa-chart-line',
            'color' => '#2563eb',
            'bg' => '#eff6ff',
            'title' => 'Dashboard',
            'summary' => 'Live KPIs, patient activity, alerts, charts, and business insights.',
            'intro' => 'The Dashboard is your control center. It updates automatically every few seconds.',
            'sections' => [
                [
                    'id' => 'live-bar',
                    'title' => 'Live activity bar',
                    'intro' => 'Top section shows real-time counts:',
                    'fields' => [
                        ['name' => 'Patients handling now', 'desc' => 'Total patients active today across OPD, IPD, lab'],
                        ['name' => 'OPD Queue', 'desc' => 'Doctor serials waiting or in consultation'],
                        ['name' => 'IPD Admitted', 'desc' => 'Patients currently admitted'],
                        ['name' => 'Lab Pending', 'desc' => 'Tests not yet marked Complete'],
                        ['name' => "Today's Collection", 'desc' => 'All payments received today'],
                    ],
                ],
                [
                    'id' => 'patient-intelligence',
                    'title' => 'Patient intelligence',
                    'steps' => [
                        'Review today\'s patient segments: Special, Regular, New, Returning, At risk.',
                        'Read prediction cards for follow-up suggestions.',
                        'Use today\'s patient table to see visit count and spend per patient.',
                    ],
                ],
                [
                    'id' => 'kpi',
                    'title' => 'Financial KPI cards',
                    'steps' => [
                        'Today collection, cost, and net — with % change vs yesterday.',
                        'Week and month net summaries below.',
                        'Click alert cards (dues, refer fee, stock) to jump to relevant pages.',
                    ],
                ],
                [
                    'id' => 'insights',
                    'title' => 'Insights panel',
                    'steps' => [
                        'Visible if you have ai.analytics permission.',
                        'Shows automated business summary from today\'s metrics.',
                        'Click refresh icon to regenerate.',
                    ],
                ],
            ],
        ],

        'patients' => [
            'group' => 'clinical',
            'icon' => 'fa-user-injured',
            'color' => '#0d9488',
            'bg' => '#ecfdf5',
            'title' => 'Patients',
            'summary' => 'Register patients, search, Patient 360 profile, and prepaid balance.',
            'intro' => 'Patients are stored in the Users/Patients module. Phone number is the main identifier.',
            'sections' => [
                [
                    'id' => 'register',
                    'title' => 'Register new patient',
                    'flow' => ['Patients → Create', 'Fill form', 'Save'],
                    'fields' => [
                        ['name' => 'Name', 'desc' => 'Full patient name'],
                        ['name' => 'Phone', 'desc' => '11-digit BD mobile (01XXXXXXXXX) — required for search'],
                        ['name' => 'Age / Gender', 'desc' => 'Used on invoices and reports'],
                        ['name' => 'Blood group', 'desc' => 'Optional, shown on profile'],
                        ['name' => 'Address', 'desc' => 'Optional'],
                    ],
                    'steps' => [
                        'Sidebar → Patients → Create.',
                        'Enter all required fields.',
                        'Save. Patient can now be used on invoices.',
                    ],
                ],
                [
                    'id' => 'search',
                    'title' => 'Quick search (navbar)',
                    'steps' => [
                        'Type name or phone in top search box (min 2 characters).',
                        'Click a result to open Patient 360.',
                    ],
                ],
                [
                    'id' => 'profile-360',
                    'title' => 'Patient 360 profile',
                    'steps' => [
                        'From patient list, click the ID card icon — or use navbar search.',
                        'View lifetime visits, spend, first/last visit dates.',
                        'Scroll timeline for all invoices, pharmacy, OPD, admits.',
                        'Check outstanding due section.',
                        'Use Clinical Overview (AI) if permitted — click Generate.',
                    ],
                ],
                [
                    'id' => 'balance',
                    'title' => 'Customer prepaid balance',
                    'steps' => [
                        'Open Customer Balances module.',
                        'Add balance against patient phone.',
                        'Balance applies on new invoices when configured in settings.',
                    ],
                ],
            ],
        ],

        'diagnostic' => [
            'group' => 'clinical',
            'icon' => 'fa-vial',
            'color' => '#7c3aed',
            'bg' => '#f5f3ff',
            'title' => 'Diagnostic & Lab',
            'summary' => 'Categories, tests, invoices, payments, lab processing, and reports.',
            'intro' => 'Core workflow for diagnostic centers.',
            'sections' => [
                [
                    'id' => 'workflow',
                    'title' => 'Complete workflow',
                    'flow' => ['Setup tests', 'Create invoice', 'Payment', 'Lab process', 'Report', 'Delivery'],
                ],
                [
                    'id' => 'catalog',
                    'title' => 'Step 1 — Setup catalog',
                    'steps' => [
                        'Categories: group tests (e.g. Biochemistry, Imaging).',
                        'Tests/Products: add each test with price, category, parameters.',
                        'Product parameters define reference ranges for lab reports.',
                    ],
                ],
                [
                    'id' => 'invoice',
                    'title' => 'Step 2 — Create invoice',
                    'flow' => ['Invoices → Create', 'Patient info', 'Add tests', 'Save'],
                    'fields' => [
                        ['name' => 'Patient name / phone', 'desc' => 'Type phone to load existing patient'],
                        ['name' => 'Referrer doctor', 'desc' => 'For commission tracking'],
                        ['name' => 'Discount', 'desc' => 'Fixed or percentage off total'],
                        ['name' => 'Test lines', 'desc' => 'Each selected test becomes one invoice line'],
                    ],
                    'steps' => [
                        'Diagnostic → Invoices → Create.',
                        'Enter patient details.',
                        'Search and add tests.',
                        'Review total, discount, refer fee.',
                        'Save invoice.',
                    ],
                ],
                [
                    'id' => 'payment',
                    'title' => 'Step 3 — Collect payment',
                    'steps' => [
                        'Open saved invoice.',
                        'Click Add Payment.',
                        'Select method: Cash, Card, Mobile banking, etc.',
                        'Enter amount (full or partial).',
                        'Due balance shows automatically on invoice.',
                    ],
                ],
                [
                    'id' => 'lab',
                    'title' => 'Step 4 — Lab processing',
                    'steps' => [
                        'Open Lab module — pending tests listed.',
                        'Open test line → change status: Pending → Processing → Complete.',
                        'Enter results in report editor.',
                        'Save report.',
                    ],
                    'warnings' => [
                        'Report PDF only available when status is Complete.',
                    ],
                ],
                [
                    'id' => 'report',
                    'title' => 'Step 5 — Report & delivery',
                    'steps' => [
                        'On invoice show page, click PDF icon on completed test line.',
                        'Toggle Delivery Complete when patient receives all reports.',
                        'Use AI Report Summary icon for staff-friendly summary (if permitted).',
                    ],
                ],
                [
                    'id' => 'refer',
                    'title' => 'Referrer commission',
                    'steps' => [
                        'Commission calculated per invoice based on referrer settings.',
                        'Pay from invoice show → Refer Payment.',
                        'Or bulk pay via Reports → Refer Payment.',
                    ],
                ],
            ],
            'faqs' => [
                ['q' => 'Why is report PDF missing?', 'a' => 'Test status must be Complete and report content must be saved in Lab module.'],
                ['q' => 'How to fix wrong patient on invoice?', 'a' => 'Edit invoice if permission allows, or create correction invoice per hospital policy.'],
            ],
        ],

        'opd' => [
            'group' => 'clinical',
            'icon' => 'fa-user-md',
            'color' => '#0891b2',
            'bg' => '#ecfeff',
            'title' => 'OPD & Doctor Serial',
            'summary' => 'Doctor queue, serial numbers, and consultation tracking.',
            'intro' => 'Manage outpatient queue per doctor per day.',
            'sections' => [
                [
                    'id' => 'serial-create',
                    'title' => 'Create doctor serial',
                    'steps' => [
                        'Doctor Serial → Create.',
                        'Select doctor, patient, date.',
                        'Save — serial number assigned.',
                    ],
                ],
                [
                    'id' => 'queue',
                    'title' => 'Manage queue',
                    'fields' => [
                        ['name' => 'Waiting', 'desc' => 'Patient in queue'],
                        ['name' => 'Checking', 'desc' => 'Currently with doctor'],
                        ['name' => 'Done', 'desc' => 'Consultation finished'],
                    ],
                    'steps' => [
                        'Open today\'s serial list.',
                        'Update status as patient progresses.',
                        'Print serial slip if needed.',
                    ],
                ],
                [
                    'id' => 'display',
                    'title' => 'Public display screen',
                    'steps' => [
                        'Hospital may use public serial URL on TV/monitor.',
                        'Shows live queue for patients in waiting area.',
                    ],
                ],
            ],
        ],

        'hospital' => [
            'group' => 'clinical',
            'icon' => 'fa-procedures',
            'color' => '#dc2626',
            'bg' => '#fef2f2',
            'title' => 'Hospital / IPD',
            'summary' => 'Admission, bed/cabin, daily services, billing, and release.',
            'intro' => 'In-patient department management.',
            'sections' => [
                [
                    'id' => 'admit',
                    'title' => 'Admit patient',
                    'flow' => ['Admits → Create', 'Patient + bed', 'Save'],
                    'steps' => [
                        'Select patient and bed/cabin.',
                        'Choose admitting doctor.',
                        'Save — patient status becomes admitted.',
                    ],
                ],
                [
                    'id' => 'services',
                    'title' => 'Daily services (Recepts)',
                    'steps' => [
                        'Open active admit.',
                        'Add Recept entries: bed charge, nursing, procedures.',
                        'Each service adds to running bill.',
                    ],
                ],
                [
                    'id' => 'release',
                    'title' => 'Release & final bill',
                    'flow' => ['Review charges', 'Collect payment', 'Release', 'Print'],
                    'steps' => [
                        'Admits → select patient → Release.',
                        'Review total charges and payments.',
                        'Collect remaining due.',
                        'Complete release and print summary.',
                    ],
                ],
                [
                    'id' => 'setup',
                    'title' => 'Setup items',
                    'steps' => [
                        'Bed/Cabin: define rooms and beds.',
                        'Service Categories & Services: define billable hospital services.',
                        'Hospital Costs: track IPD-specific expenses.',
                    ],
                ],
            ],
        ],

        'pharmacy' => [
            'group' => 'business',
            'icon' => 'fa-pills',
            'color' => '#16a34a',
            'bg' => '#f0fdf4',
            'title' => 'Pharmacy',
            'summary' => 'Catalog setup, stock in, POS sales, and stock alerts.',
            'intro' => 'Complete pharmacy module from setup to daily sales.',
            'sections' => [
                [
                    'id' => 'setup',
                    'title' => 'One-time setup',
                    'flow' => ['Categories', 'Brands', 'Types', 'Units', 'Products'],
                    'steps' => [
                        'Create pharmacy categories, brands, types, units.',
                        'Add products with sale price and alert quantity.',
                        'Alert qty triggers low-stock warning on dashboard.',
                    ],
                ],
                [
                    'id' => 'purchase',
                    'title' => 'Stock in (purchase)',
                    'steps' => [
                        'Pharmacy Purchases → Create.',
                        'Select supplier, add items with qty, batch, expiry.',
                        'Save — stock increases automatically.',
                    ],
                ],
                [
                    'id' => 'sale',
                    'title' => 'POS sale',
                    'flow' => ['Pharmacy Sales → Create', 'Add items', 'Payment', 'Print'],
                    'steps' => [
                        'Search products and add to cart.',
                        'Apply discount if needed.',
                        'Save and record payment (full or partial).',
                        'Print receipt via PDF preview.',
                    ],
                ],
                [
                    'id' => 'due',
                    'title' => 'Collect sale due',
                    'steps' => [
                        'Open sale with outstanding balance.',
                        'Use Pay Due action.',
                        'Enter amount and save.',
                    ],
                ],
            ],
            'faqs' => [
                ['q' => 'Stock shows zero but we purchased?', 'a' => 'Verify purchase was saved. Check correct product selected on sale.'],
            ],
        ],

        'finance' => [
            'group' => 'business',
            'icon' => 'fa-coins',
            'color' => '#ca8a04',
            'bg' => '#fefce8',
            'title' => 'Finance',
            'summary' => 'Costs, earnings, payments, and balance understanding.',
            'intro' => 'Track money in and out beyond invoice collections.',
            'sections' => [
                [
                    'id' => 'cost',
                    'title' => 'Record expense (Cost)',
                    'steps' => [
                        'Costs → Create.',
                        'Select cost category, amount, date, note.',
                        'Save — appears in reports and reduces net balance.',
                    ],
                ],
                [
                    'id' => 'earn',
                    'title' => 'Record other income (Earn)',
                    'steps' => [
                        'Earns → Create.',
                        'Enter source description and amount.',
                        'Save.',
                    ],
                ],
                [
                    'id' => 'balance',
                    'title' => 'How balance is calculated',
                    'intro' => 'Current Balance = Diagnostic Collection + Hospital Collection + Earns − Costs',
                    'steps' => [
                        'View summary in Reports → Monthly Balance.',
                        'Use Day-wise Balance for daily breakdown.',
                        'Dashboard shows today\'s net estimate.',
                    ],
                ],
            ],
        ],

        'reports' => [
            'group' => 'business',
            'icon' => 'fa-chart-bar',
            'color' => '#4f46e5',
            'bg' => '#eef2ff',
            'title' => 'Reports',
            'summary' => 'Collections, balance, refer fees, costs, and pharmacy stock reports.',
            'intro' => 'All reports support date range filter and PDF export.',
            'sections' => [
                [
                    'id' => 'how-to',
                    'title' => 'How to run any report',
                    'steps' => [
                        'Open report from sidebar → Reports.',
                        'Set From date and To date.',
                        'Click Filter / Search.',
                        'Export PDF or Print.',
                    ],
                ],
                [
                    'id' => 'list',
                    'title' => 'Report types',
                    'fields' => [
                        ['name' => 'Monthly Balance', 'desc' => 'Collection, cost, net by month'],
                        ['name' => 'Day-wise Balance', 'desc' => 'Daily cash flow table'],
                        ['name' => 'Diagnostic Collections', 'desc' => 'Invoice payment income'],
                        ['name' => 'Hospital Collections', 'desc' => 'IPD payment income'],
                        ['name' => 'Sales by Category', 'desc' => 'Test sales breakdown'],
                        ['name' => 'Refer Commission', 'desc' => 'Owed referrer fees'],
                        ['name' => 'Refer Payment', 'desc' => 'Pay referrers'],
                        ['name' => 'Cost Report', 'desc' => 'Expense by category'],
                        ['name' => 'Pharmacy Stock', 'desc' => 'Current stock levels'],
                    ],
                ],
            ],
        ],

        'hr' => [
            'group' => 'business',
            'icon' => 'fa-id-badge',
            'color' => '#9333ea',
            'bg' => '#faf5ff',
            'title' => 'HR & Attendance',
            'summary' => 'Employees, attendance, leave, and salary sheets.',
            'sections' => [
                [
                    'id' => 'employee',
                    'title' => 'Add employee',
                    'steps' => [
                        'Employees → Create.',
                        'Fill personal info, designation, salary.',
                        'If HR system update applied: set weekly off, working hours, leave quota.',
                        'Save.',
                    ],
                ],
                [
                    'id' => 'attendance',
                    'title' => 'Attendance',
                    'steps' => [
                        'Fingerprint device may auto-record (if integrated).',
                        'Or manage manually in Attendance module.',
                        'View monthly sheet from employee profile.',
                    ],
                ],
                [
                    'id' => 'salary',
                    'title' => 'Salary sheet',
                    'steps' => [
                        'Employees → select employee → Salary Sheet.',
                        'Generate and print for payroll.',
                    ],
                ],
            ],
        ],

        'inventory' => [
            'group' => 'business',
            'icon' => 'fa-warehouse',
            'color' => '#0369a1',
            'bg' => '#e0f2fe',
            'title' => 'General Inventory',
            'summary' => 'Suppliers, items, purchases, and reagent stock for lab.',
            'intro' => 'Non-pharmacy inventory: lab reagents, hospital supplies, etc.',
            'sections' => [
                [
                    'id' => 'setup',
                    'title' => 'Setup',
                    'flow' => ['Suppliers', 'Items', 'Purchases'],
                    'steps' => [
                        'Suppliers: add vendor names and contact.',
                        'Items: define inventory products with unit.',
                        'Purchases: stock in with batch and expiry date.',
                    ],
                ],
                [
                    'id' => 'purchase',
                    'title' => 'Stock in',
                    'steps' => [
                        'Purchases → Create.',
                        'Select supplier, add line items with qty and expiry.',
                        'Save — quantity available for lab reagent tracking.',
                    ],
                ],
                [
                    'id' => 'alerts',
                    'title' => 'Expiry alerts',
                    'steps' => [
                        'Dashboard may show expiry alerts for items expiring in 7 days.',
                        'Check purchase items with high usage (>90% spent).',
                    ],
                ],
            ],
        ],

        'admin' => [
            'group' => 'system',
            'icon' => 'fa-shield-alt',
            'color' => '#475569',
            'bg' => '#f8fafc',
            'title' => 'Administration',
            'summary' => 'Branches, roles, admin users, settings, and audit logs.',
            'sections' => [
                [
                    'id' => 'settings',
                    'title' => 'Hospital settings',
                    'steps' => [
                        'Main → Settings.',
                        'Configure: name, logo, address, invoice header.',
                        'SMS formats for patient and doctor notifications.',
                        'Save each section.',
                    ],
                ],
                [
                    'id' => 'users',
                    'title' => 'Create staff account',
                    'steps' => [
                        'Administration → Admin Users → Create.',
                        'Name, email, password, branch.',
                        'Assign role → Save.',
                    ],
                ],
                [
                    'id' => 'roles',
                    'title' => 'Manage permissions',
                    'steps' => [
                        'Administration → Roles → Edit role.',
                        'Enable permissions per module.',
                        'Staff must re-login to see new menus.',
                    ],
                ],
                [
                    'id' => 'branches',
                    'title' => 'Multi-branch',
                    'steps' => [
                        'Administration → Branches to add locations.',
                        'Each admin user belongs to one branch.',
                        'Data is filtered by branch automatically.',
                    ],
                ],
            ],
        ],

        'ai' => [
            'group' => 'system',
            'icon' => 'fa-brain',
            'color' => '#0f766e',
            'bg' => '#ecfdf5',
            'title' => 'AI Features',
            'summary' => 'Report summary, clinical overview, assistant chat, and insights.',
            'sections' => [
                [
                    'id' => 'setup',
                    'title' => 'Enable AI tables',
                    'steps' => [
                        'Super Admin → Dashboard → System Updates.',
                        'Apply AI Features module.',
                        'Assign ai.* permissions in Roles.',
                    ],
                ],
                [
                    'id' => 'features',
                    'title' => 'Available features',
                    'fields' => [
                        ['name' => 'Report Summary', 'desc' => 'Invoice → completed test → summary icon'],
                        ['name' => 'Clinical Overview', 'desc' => 'Patient 360 → Generate'],
                        ['name' => 'Assistant', 'desc' => 'Chat button bottom-right'],
                        ['name' => 'Insights', 'desc' => 'Dashboard insights panel'],
                    ],
                ],
                [
                    'id' => 'api',
                    'title' => 'Optional API key (server)',
                    'steps' => [
                        'Server admin adds AI_API_KEY to .env file.',
                        'Run php artisan config:clear.',
                        'Without key, built-in responses still work.',
                    ],
                ],
            ],
        ],

        'system' => [
            'group' => 'system',
            'icon' => 'fa-database',
            'color' => '#b45309',
            'bg' => '#fffbeb',
            'title' => 'System Updates',
            'summary' => 'Safe database updates from dashboard — no data loss.',
            'sections' => [
                [
                    'id' => 'apply',
                    'title' => 'How to apply update',
                    'steps' => [
                        'Login as Super Admin.',
                        'Dashboard → System Updates panel.',
                        'Check module status — Pending or Ready.',
                        'Click Apply on pending module.',
                        'Confirm dialog → wait for success.',
                        'Refresh page.',
                    ],
                    'warnings' => [
                        'Always backup database before production updates.',
                        'Never run migrate:fresh — it deletes all data.',
                    ],
                ],
                [
                    'id' => 'modules',
                    'title' => 'Update modules',
                    'fields' => [
                        ['name' => 'Audit Logs', 'desc' => 'Change history table'],
                        ['name' => 'HR Schedule', 'desc' => 'Employee schedule columns'],
                        ['name' => 'Pharmacy Status', 'desc' => 'Active/inactive on products'],
                        ['name' => 'AI Features', 'desc' => 'Chat and summary tables'],
                    ],
                ],
            ],
        ],

        'troubleshooting' => [
            'group' => 'system',
            'icon' => 'fa-life-ring',
            'color' => '#e11d48',
            'bg' => '#fff1f2',
            'title' => 'Troubleshooting',
            'summary' => 'Common problems and step-by-step fixes.',
            'sections' => [],
            'faqs' => [
                ['q' => 'Cannot login?', 'a' => 'Check password, subscription status, and account is active. Use Forgot Password or contact Super Admin.'],
                ['q' => 'Menu missing?', 'a' => 'Your role lacks permission. Super Admin → Roles → enable module → re-login.'],
                ['q' => 'Invoice due not updating?', 'a' => 'Open invoice → Add Payment → save amount → refresh page.'],
                ['q' => 'Lab report not showing?', 'a' => 'Status must be Complete. Report must be saved in Lab module.'],
                ['q' => 'Pharmacy stock wrong?', 'a' => 'Verify purchase saved. Check product on sale matches. Run Pharmacy Stock report.'],
                ['q' => 'AI not working?', 'a' => 'Apply AI Features system update. Check role has ai.* permissions. Hard refresh browser.'],
                ['q' => 'Dashboard slow?', 'a' => 'Live updates every 3 seconds is normal. Check internet and server. Super Admin can clear cache.'],
                ['q' => 'SMS not sending?', 'a' => 'Check SMS Balance on dashboard. Verify phone format. Recharge SMS credits.'],
            ],
        ],
    ],
];

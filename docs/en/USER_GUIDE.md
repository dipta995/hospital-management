# Hospital Management System — User Guide (English)

Complete step-by-step guide for diagnostic centers and hospitals.  
Admin panel URL: **`/admin/login`**

---

## Table of contents

1. [Getting started](#1-getting-started)
2. [Dashboard](#2-dashboard)
3. [Patients](#3-patients)
4. [Diagnostic & Lab workflow](#4-diagnostic--lab-workflow)
5. [OPD (Doctor serial)](#5-opd-doctor-serial)
6. [IPD / Hospital admits](#6-ipd--hospital-admits)
7. [Pharmacy](#7-pharmacy)
8. [Finance](#8-finance)
9. [Reports](#9-reports)
10. [HR & Attendance](#10-hr--attendance)
11. [Settings, roles & users](#11-settings-roles--users)
12. [AI features](#12-ai-features)
13. [System updates (Super Admin)](#13-system-updates-super-admin)
14. [Troubleshooting & FAQ](#14-troubleshooting--faq)

---

## 1. Getting started

### 1.1 First login

**Step 1:** Open your browser and go to:

```
https://your-hospital-domain.com/admin/login
```

**Step 2:** Enter your **email** and **password** provided by your administrator.

**Step 3:** Click **Login**. You will land on the **Dashboard**.

> **Default Super Admin** (only after fresh install + seed):  
> Email: `superadmin@email.com` · Password: `12344321`  
> **Change this password immediately** after first login.

### 1.2 Change your password

1. Click your profile / password menu (top right).
2. Go to **Change Password**.
3. Enter old password → new password → confirm.
4. Save.

### 1.3 Language (English / Bangla)

1. Use the **language switch** in the top navbar.
2. The interface will reload in your selected language.
3. This guide is available in both English and Bangla in the `docs/` folder.

### 1.4 What you see depends on your role

- Each staff member has a **Role** (e.g. Reception, Lab, Accountant).
- Each role has **Permissions** (view, create, edit, delete per module).
- If a menu is missing, ask Super Admin to grant permission via **Administration → Roles**.

### 1.5 Subscription notice

If your hospital subscription is near expiry, a banner may appear at the top. Contact your software provider and complete payment before the end date to avoid login restrictions.

---

## 2. Dashboard

The dashboard (`/admin`) shows live hospital activity.

### 2.1 Live activity bar

Updates every few seconds:

| Item | Meaning |
|------|---------|
| **Patients handling now** | Total active patients today |
| **OPD Queue** | Waiting + checking doctor serials |
| **IPD Admitted** | Current in-patients |
| **Lab Pending** | Tests not yet completed |
| **Today's Collection** | Money received today |

### 2.2 Patient intelligence

- Shows today's patient segments: Special, Regular, New, Returning, etc.
- Predictions help identify follow-up or at-risk patients.
- Click patient rows to understand visit patterns.

### 2.3 Insights (AI)

If you have **ai.analytics** permission:

1. Open Dashboard.
2. Find the **Insights** panel.
3. Read automated business summary.
4. Click the **refresh** icon to regenerate.

### 2.4 KPI cards

- Today's collection, cost, net profit
- Week / month comparisons
- Outstanding dues, refer fee due, pharmacy sales, SMS balance

### 2.5 Quick actions

Use shortcut buttons for: New Invoice, Pharmacy Sale, Collections report, Doctor Serial, etc. (based on your permissions).

---

## 3. Patients

### 3.1 Register a new patient

1. Sidebar → **Patients** (or Users) → **Create**.
2. Fill: Name, Phone, Age, Gender, Blood group, Address.
3. Click **Save**.

> Phone number must be valid BD format (11 digits, starts with 01).

### 3.2 Search patient (navbar)

1. Type **name or phone** in the top search box (minimum 2 characters).
2. Click a result to open **Patient 360** profile.

### 3.3 Patient 360 profile

Open via search or: **Patients → List → ID card icon**

You will see:

- Visit count, lifetime spend, first/last visit
- Full timeline: invoices, pharmacy, OPD, admits
- Outstanding due amounts
- Segment label (Special / Regular / New / At risk)
- **Clinical Overview** (AI) — click **Generate** if permitted

### 3.4 Customer balance (prepaid)

If your hospital uses prepaid balance:

1. Go to **Customer Balances**.
2. Add balance for a patient phone number.
3. Balance auto-applies on new invoices when configured.

---

## 4. Diagnostic & Lab workflow

This is the core flow: **Invoice → Lab processing → Test report → Delivery**

### 4.1 Create diagnostic invoice

**Step 1:** Sidebar → **Invoices** → **Create** (or Dashboard → New Invoice)

**Step 2:** Enter patient details (or select existing patient by phone)

**Step 3:** Add tests from the product list

**Step 4:** Set discount, referrer doctor if any

**Step 5:** Save invoice

**Step 6:** Collect payment:
- Open invoice → **Add Payment**
- Choose method: Cash, Card, Mobile banking, etc.
- Enter amount → Save

### 4.2 Send tests to lab

**Step 1:** Sidebar → **Lab** → pending tests list

**Step 2:** Open a test line → update status:
- `Pending` → `Processing` → `Complete`

**Step 3:** Enter test results in the report editor (Summernote / template)

**Step 4:** Save report

### 4.3 Print / download report

1. Open **Invoice** → view test lines.
2. When status is **Complete**, use:
   - **PDF icon** — view/print lab report
   - **Download icon** — attached document file
3. Or use **Test Reports** module for template-based reports.

### 4.4 AI Report Summary

If you have **ai.reports** permission:

1. Open invoice with completed test.
2. Click the **document/summary icon** on the test line.
3. A summary modal opens (plain-language overview for staff).
4. Summary is saved and reused on next open.

### 4.5 Mark delivery complete

On invoice show page, toggle **Delivery Complete** when all reports are handed to the patient.

### 4.6 Referrer commission

If a referring doctor is linked:

1. Invoice show → **Refer Payment** when commission is due.
2. Or use **Reports → Refer Payment** for bulk settlement.

### 4.7 SMS to patient (if enabled)

SMS may auto-send on invoice create if configured in **Settings → SMS formats**.  
Check **SMS Balance** on dashboard; recharge via administrator if low.

---

## 5. OPD (Doctor serial)

### 5.1 Create doctor serial

1. Sidebar → **Doctor Serial** → **Create**
2. Select doctor, patient, date
3. Save — patient gets a queue number

### 5.2 Manage queue

1. Open **Doctor Serial** list for today
2. Update status: Waiting → Checking → Done
3. Print serial slip if needed

### 5.3 Public serial display

Hospitals can show a public queue screen via the serial display URL (configured per branch/doctor room).

---

## 6. IPD / Hospital admits

### 6.1 Admit a patient

1. Sidebar → **Admits** → **Create**
2. Select patient, bed/cabin, admitting doctor
3. Save

### 6.2 Daily hospital services (Recepts)

1. Open active admit
2. Add services (bed charge, nursing, procedures) via **Recepts**
3. Services accumulate on the admit bill

### 6.3 Release & final bill

1. **Admits** → select patient → **Release**
2. Review total charges and payments
3. Collect remaining due → complete release
4. Print release summary

### 6.4 Hospital collection in reports

Hospital payments appear separately in **Reports → Hospital Collections**.

---

## 7. Pharmacy

### 7.1 One-time setup

Do this once before selling:

| Step | Menu | Action |
|------|------|--------|
| 1 | Pharmacy → Categories | Create medicine categories |
| 2 | Pharmacy → Brands | Add brands |
| 3 | Pharmacy → Types / Units | Add types and units |
| 4 | Pharmacy → Products | Add products with price, stock alert qty |
| 5 | Pharmacy → Purchases | Stock in medicines from supplier |

### 7.2 Stock in (purchase)

1. **Pharmacy Purchases** → **Create**
2. Select supplier, add line items (product, qty, batch, expiry)
3. Save — stock increases automatically

### 7.3 POS sale

1. **Pharmacy Sales** → **Create**
2. Search/add products to cart
3. Apply discount if needed
4. Save and collect payment
5. Print receipt (PDF preview)

### 7.4 Due payment on sale

1. Open sale with due balance
2. Use **Pay Due** to collect remaining amount

### 7.5 Low stock alert

Dashboard shows pharmacy stock alerts when products are out or below alert quantity. Restock via **Pharmacy Purchases**.

---

## 8. Finance

### 8.1 Record expense (Cost)

1. **Costs** → **Create**
2. Select category, amount, date, note
3. Save

### 8.2 Record other income (Earn)

1. **Earns** → **Create**
2. Enter source, amount, date
3. Save

### 8.3 Payments (general)

Use **Payments** module for non-invoice payments as configured by your hospital.

### 8.4 Understanding balance

**Current balance** ≈ Collections (diagnostic + hospital) + Earns − Costs

View in **Reports → Monthly Balance** or **Day-wise Balance**.

---

## 9. Reports

All reports support date filters and PDF export where available.

| Report | Path | Use for |
|--------|------|---------|
| Monthly balance | Reports → Monthly Balance | Profit/loss by month |
| Day-wise balance | Reports → Day-wise Balance | Daily cash flow |
| Diagnostic collections | Reports → Diagnostic Collections | Lab/invoice income |
| Hospital collections | Reports → Hospital Collections | IPD income |
| Sales by category | Reports → Sales by Category | Test category breakdown |
| Refer commission | Reports → Refer Commission | Doctor referral fees |
| Refer payment | Reports → Refer Payment | Pay referrers |
| Cost report | Reports → Cost Report | Expense breakdown |
| Pharmacy stock | Reports → Pharmacy Stock | Current stock levels |

**How to run any report:**

1. Open report from sidebar
2. Set **From date** and **To date**
3. Click **Filter** / **Search**
4. Click **PDF** or **Print** to export

---

## 10. HR & Attendance

### 10.1 Add employee

1. **Employees** → **Create**
2. Fill personal info, salary, schedule fields (if HR schema installed)
3. Save

### 10.2 Attendance

- Fingerprint/device integration may auto-record attendance
- Manual attendance can be managed from **Attendance** module
- View monthly sheet from employee profile

### 10.3 Leave days

If **HR Schedule** system update is applied:

1. Employee profile → configure weekly off, working hours, leave quota
2. Record leave days in **Employee Leave Days**

### 10.4 Salary sheet

**Employees** → select employee → **Salary Sheet** → generate/print

---

## 11. Settings, roles & users

### 11.1 Hospital settings

**Settings** (`/admin/settings`) — configure:

- Hospital name, logo, address
- Invoice / report headers
- SMS message formats for patient and doctor
- Subscription payment rules (Super Admin)

Save each section after editing.

### 11.2 Create admin user

1. **Administration → Admin Users** → **Create**
2. Name, email, password, branch
3. Assign **Role**
4. Save

### 11.3 Create / edit role

1. **Administration → Roles** → **Create** or edit existing
2. Check permissions per module (view, create, edit, delete)
3. Save

**AI permissions** (assign as needed):

| Permission | Allows |
|------------|--------|
| `ai.reports` | Report summary on invoices |
| `ai.health` | Clinical overview on Patient 360 |
| `ai.chat` | Assistant chat widget |
| `ai.analytics` | Dashboard insights |

### 11.4 Branches

Multi-branch hospitals: **Administration → Branches** to add branches. Each admin user is tied to one branch; data is filtered by branch.

### 11.5 Audit logs

If **Audit Logs** system update is installed: **Audit Logs** menu shows who changed what (Super Admin).

---

## 12. AI features

AI works with or without an API key. Without a key, the system uses built-in rule-based responses.

### 12.1 Enable AI API (optional, server admin)

Add to server `.env` file:

```env
AI_ENABLED=true
AI_API_KEY=your-api-key-here
AI_MODEL=gpt-4o-mini
```

Then run: `php artisan config:clear`

### 12.2 Install AI database tables

**Super Admin only:**

1. Dashboard → **System Updates**
2. Find **AI Features** card
3. If status is **Pending**, click **Apply**
4. Wait for success message — no existing data is deleted

### 12.3 Assistant chat

1. Look for the chat button (bottom-right corner)
2. Click to open **Assistant**
3. Type questions: e.g. "Where is pharmacy sales?", "How to check pending labs?"
4. Press send or Enter

### 12.4 Report summary

See [§4.4](#44-ai-report-summary)

### 12.5 Clinical overview

See [§3.3](#33-patient-360-profile) — Patient 360 → **Generate**

### 12.6 Business insights

See [§2.3](#23-insights-ai)

---

## 13. System updates (Super Admin)

When new features need database changes, apply updates from the dashboard — **no data is removed**.

### 13.1 How to apply

1. Login as **Super Admin**
2. Open **Dashboard**
3. Scroll to **System Updates** panel
4. Each module shows status chips (green = ready)
5. For **Pending** modules, click **Apply**
6. Confirm the dialog
7. Refresh page — status should show **Ready**

### 13.2 Available modules

| Module | What it adds |
|--------|----------------|
| **Audit Logs** | Change tracking table |
| **HR Schedule & Leave** | Employee schedule columns, leave table |
| **Pharmacy Status** | Active/inactive flag on pharmacy items |
| **AI Features** | Chat tables, insights cache, report summary column |

### 13.3 Before production update

1. **Backup database** (export from phpMyAdmin or `mysqldump`)
2. Apply update during low-traffic time
3. Test login and one invoice flow after update

### 13.4 Clear cache

If menus or settings look wrong after deploy:

- Super Admin can clear cache (contact developer or run `php artisan cache:clear` on server)

---

## 14. Troubleshooting & FAQ

### Cannot login

| Problem | Solution |
|---------|----------|
| Wrong password | Use **Forgot password** or ask Super Admin to reset |
| Subscription expired | Complete payment; contact software provider |
| Account disabled | Super Admin checks **Admin Users** |

### Menu / page not visible

1. Confirm you are logged in with correct role
2. Super Admin → **Roles** → edit your role → enable permission
3. Logout and login again

### Invoice due not updating

1. Open invoice → **Add Payment**
2. Ensure payment amount is saved
3. Refresh invoice page

### Lab report not showing

1. Check test status is **Complete**
2. Ensure report content was saved in Lab module
3. Check **Test Reports** template if using demo templates

### Pharmacy stock wrong

1. Verify purchase was saved (Pharmacy Purchases list)
2. Check product name matches on sale
3. Run **Reports → Pharmacy Stock** to audit

### AI insights not loading

1. Check **ai.analytics** permission on your role
2. Super Admin: apply **AI Features** system update
3. If using API: verify `AI_API_KEY` in `.env` and run `config:clear`

### AI chat not appearing

1. Need **ai.chat** permission
2. Apply **AI Features** system update
3. Hard refresh browser (Ctrl+F5)

### "System Updates" not visible

Only **Super Admin** role sees this panel.

### Database error after update

1. Do not run `migrate:fresh` — it deletes all data
2. Super Admin → Dashboard → apply pending system update
3. If still failing, restore from backup and contact support with error screenshot

### Slow dashboard

- Normal: dashboard polls live data every 3 seconds
- Check internet and server load
- Super Admin can clear cache

### SMS not sending

1. Check **SMS Balance** on dashboard (must be > 0)
2. Verify patient phone format in Settings/SMS template
3. Recharge SMS credits with provider

---

## Support checklist

When contacting support, provide:

1. Your hospital / branch name
2. Admin email (not password)
3. Screenshot of error
4. What you clicked (step by step)
5. Date and time of issue

---

*Last updated: June 2026 · Hospital Management System*

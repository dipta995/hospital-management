<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AiController;
use App\Http\Controllers\Backend\AiModuleController;
use App\Http\Controllers\Backend\ApiController;
use App\Http\Controllers\Backend\AuditLogController;
use App\Http\Controllers\Backend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Backend\Auth\PasswordResetLinkController;
use App\Http\Controllers\Backend\BranchController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ServiceCategoryController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\CostCategoryController;
use App\Http\Controllers\Backend\CostController;
use App\Http\Controllers\Backend\DocumentationController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DoctorRoomController;
use App\Http\Controllers\Backend\DoctorSerialController;
use App\Http\Controllers\Backend\EarnController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\InvoiceController;
use App\Http\Controllers\Backend\InvoiceListController;
use App\Http\Controllers\Backend\ItemController;
use App\Http\Controllers\Backend\LabController;
use App\Http\Controllers\Backend\PharmacyCategoryController;
use App\Http\Controllers\Backend\PharmacyTypeController;
use App\Http\Controllers\Backend\PharmacyBrandController;
use App\Http\Controllers\Backend\PharmacyUnitController;
use App\Http\Controllers\Backend\PharmacyProductController;
use App\Http\Controllers\Backend\PharmacyPurchaseController;
use App\Http\Controllers\Backend\PharmacySaleController;
use App\Http\Controllers\Backend\NumberCategoryController;
use App\Http\Controllers\Backend\PatientController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\PhoneNumberController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\PurchaseItemController;
use App\Http\Controllers\Backend\ReeferController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\RolesController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\TestReportController;
use App\Http\Controllers\Backend\TestReportDemoController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\AdmitController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Backend\ReceptController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Backend\SystemMaintenanceController;
use App\Http\Controllers\Backend\EmployeeLeaveDayController;
use App\Http\Controllers\Backend\BedCabinController;
use App\Http\Controllers\Backend\CustomerBalanceController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\SubscriptionPublicController;
use App\Http\Controllers\SerialController;
use App\Models\DoctorSerial;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\PrescriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/serials/public/{uniqueCode}', [SerialController::class, 'indexPublic'])->name('doctor.serials.indexPublic');
Route::get('/subscription/payment/{token}', [SubscriptionPublicController::class, 'show'])->name('subscription.payment.public');
Route::post('/subscription/payment/{token}', [SubscriptionPublicController::class, 'store'])->name('subscription.payment.public.submit');
Route::get('/subscription/payment-list/{token}', [SubscriptionPublicController::class, 'paymentList'])->name('subscription.payment.list.public');
Route::post('/subscription/payment-list/{token}/approve/{requestRow}', [SubscriptionPublicController::class, 'approveFromList'])->name('subscription.payment.list.approve');
Route::post('/subscription/payment-list/{token}/reject/{requestRow}', [SubscriptionPublicController::class, 'rejectFromList'])->name('subscription.payment.list.reject');
Route::get('/serials/{uniqueCode}', [SerialController::class, 'index'])->name('doctor.serials.index');
Route::get('/serials/list/{dr}/{branchId}', [SerialController::class, 'serialLists']);
Route::get('/', function () {
    return view('welcome');
});

Route::post('/update-serial-status', [SerialController::class, 'updateSerialStatus']);

// Route for the print view
Route::get('/doctor-serials/print', [DoctorSerialController::class, 'print'])->name('admin.doctor_serials.print');

// Route::middleware(['auth'])->group(function () {
//     Route::get('/doctor-serials/print', [DoctorSerialController::class, 'print'])->name('admin.doctor_serials.print');
// });


//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});

Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login/submit', [AuthenticatedSessionController::class, 'store'])->name('login.submit');
    Route::get('/logout/submit', [AuthenticatedSessionController::class, 'destroy'])->name('logout.submit');

    Route::get('/password/change', [AuthenticatedSessionController::class, 'change'])->name('change');
    Route::post('/password/change/pw', [AuthenticatedSessionController::class, 'changePw'])->name('change-pw');
    Route::get('/password/reset', [PasswordResetLinkController::class, 'create'])->name('password');
    Route::post('/password/reset/submit', [PasswordResetLinkController::class, 'destroy'])->name('password.submit');
});

// Backend Start
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'subscription.access']], function () {

    Route::get('/help', [DocumentationController::class, 'index'])->name('help.index');
    Route::get('/help/{locale}/{module}', [DocumentationController::class, 'show'])->name('help.show')->where(['locale' => 'en|bn', 'module' => '[a-z_]+']);
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard/live', [DashboardController::class, 'liveStats'])->name('dashboard.live');
    Route::get('/dashboard/ai-insights', [AiController::class, 'businessInsights'])->name('dashboard.ai-insights');
    Route::post('/system/clear-cache', [SystemMaintenanceController::class, 'clearCache'])->name('system.clear-cache');
    Route::get('/system/schema-status', [SystemMaintenanceController::class, 'schemaStatus'])->name('system.schema-status');
    Route::post('/system/install-audit-log-schema', [SystemMaintenanceController::class, 'installAuditLogSchema'])->name('system.install-audit-log-schema');
    Route::post('/system/install-hr-schema', [SystemMaintenanceController::class, 'installHrSchema'])->name('system.install-hr-schema');
    Route::post('/system/install-schema/{key}', [SystemMaintenanceController::class, 'installSchema'])->name('system.install-schema');
    Route::get('/ai', [AiModuleController::class, 'index'])->name('ai.index');
    Route::post('/ai/report-summary', [AiController::class, 'reportSummary'])->name('ai.report-summary');
    Route::post('/ai/health-explain', [AiController::class, 'healthExplain'])->name('ai.health-explain');
    Route::post('/ai/chat', [AiController::class, 'chat'])->name('ai.chat');
    Route::get('/ai/chat/history', [AiController::class, 'chatHistory'])->name('ai.chat.history');
    Route::get('/ai/chat/suggestions', [AiController::class, 'chatSuggestions'])->name('ai.chat.suggestions');
    Route::get('/ai/advanced-analytics', [AiController::class, 'advancedAnalytics'])->name('ai.advanced-analytics');
//    Roles
    Route::resource('roles', RolesController::class, ['names' => 'roles']);
//    Admins
    Route::resource('admins', AdminController::class, ['names' => 'admins']);
//    Users
    Route::resource('users', UserController::class, ['names' => 'users']);
    Route::get('/patients/profile', [PatientController::class, 'profile'])->name('patients.profile');
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

//    CrudGenerator
//    Profile Edit
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('/settings', [SettingController::class, 'destroy'])->name('settings.destroy');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/date', [SubscriptionController::class, 'updateDate'])->name('subscriptions.date.update');
    Route::post('/subscriptions/requests/{id}/approve', [SubscriptionController::class, 'approveRequest'])->name('subscriptions.requests.approve');
    Route::post('/subscriptions/requests/{id}/reject', [SubscriptionController::class, 'rejectRequest'])->name('subscriptions.requests.reject');

    //    Accounts
    Route::resource('branches', BranchController::class, ['names' => 'branches']);
    Route::resource('categories', CategoryController::class, ['names' => 'categories']);
    Route::resource('pharmacy-categories', PharmacyCategoryController::class, ['names' => 'pharmacy_categories']);
    Route::resource('pharmacy-types', PharmacyTypeController::class, ['names' => 'pharmacy_types']);
    Route::resource('pharmacy-brands', PharmacyBrandController::class, ['names' => 'pharmacy_brands']);
    Route::resource('pharmacy-units', PharmacyUnitController::class, ['names' => 'pharmacy_units']);
    Route::resource('pharmacy-purchases', PharmacyPurchaseController::class, ['names' => 'pharmacy_purchases']);
    Route::resource('pharmacy-products', PharmacyProductController::class, ['names' => 'pharmacy_products']);
    Route::resource('pharmacy-sales', PharmacySaleController::class, ['names' => 'pharmacy_sales']);
    Route::get('/pharmacy-sales/pdf-preview/{id}', [PharmacySaleController::class, 'pdfPreview'])->name('pharmacy_sales.pdf-preview');
    Route::post('/pharmacy-sales/due-pay/{id}', [PharmacySaleController::class, 'payDue'])->name('pharmacy_sales.due-pay');
    Route::resource('admits', AdmitController::class, ['names' => 'admits']);
    Route::post('admits/{id}/release', [AdmitController::class, 'storeRelease'])->name('admits.release');
    Route::get('admits/{id}/release-details', [AdmitController::class, 'releaseDetails'])->name('admits.release.details');
    Route::get('admits/{id}/release-print', [AdmitController::class, 'releasePrint'])->name('admits.release.print');
    Route::post('admits/{id}/pay-due', [AdmitController::class, 'payDue'])->name('admits.pay-due');
    Route::post('admits/{id}/pc-payment', [AdmitController::class, 'pcPayment'])->name('admits.pc-payment');
    Route::post('admits/{id}/hospital-cost', [AdmitController::class, 'hospitalCost'])->name('admits.hospital-cost');
    Route::get('admits/{id}/print', [AdmitController::class, 'print'])->name('admits.print');

    Route::resource('recepts', ReceptController::class, ['names' => 'recepts']);
    Route::post('recepts/{id}/pay', [ReceptController::class, 'pay'])->name('recepts.pay');
    Route::get('/recepts/pdf-preview/{id}', [ReceptController::class, 'receptPdfPreview'])->name('recepts.pdf-preview');
    Route::get('/hospital-costs', [CostController::class, 'hospitalIndex'])->name('hospital_costs.index');
    Route::resource('service-categories', ServiceCategoryController::class, ['names' => 'service_categories']);
    Route::resource('services', ServiceController::class, ['names' => 'services']);

    Route::resource('bed-cabins', BedCabinController::class, ['names' => 'bed_cabins']);

    Route::resource('customer-balances', CustomerBalanceController::class, ['names' => 'customer_balances']);

    Route::resource('products', ProductController::class, ['names' => 'products']);
    Route::resource('invoices', InvoiceController::class, ['names' => 'invoices']);
    Route::get('/invoices/pdf-preview/{id}', [InvoiceController::class, 'pdfPreview'])->name('invoices.pdf-preview');
    Route::get('/report/pdf-preview/{id}', [InvoiceController::class, 'reportPdfPreview'])->name('lab.report.pdf-preview');
    Route::get('/report/file-download/{id}', [InvoiceController::class, 'reportfileDownload'])->name('lab.report.file-download');
    Route::get('/invoices/status/{id}', [InvoiceController::class, 'invoiceStatus'])->name('invoices.test.status');
    Route::post('/invoices/due/pay/{id}', [InvoiceController::class, 'invoiceDuePay'])->name('invoices.due-pay');
    Route::get('/trash', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/trash/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    Route::resource('invoice_lists', InvoiceListController::class, ['names' => 'invoice_lists']);
    Route::get('reefers/{id}/commission', [ReeferController::class, 'commission'])->name('reefers.commission');
    Route::resource('reefers', ReeferController::class, ['names' => 'reefers']);
    Route::post('reefers/store-api', [ReeferController::class, 'storeApi'])->name('reefers.store.api');
    Route::get('reefers/custom/sms',[ReeferController::class,'customSms'])->name('reefers.custom-sms');
    Route::get('reefers/custom/sms/send',[ReeferController::class,'customSmsSend'])->name('reefers.custom-sms-send');
    Route::get('employees/salary-sheet', [EmployeeController::class, 'salarySheet'])->name('employees.salary-sheet');
    Route::get('employees/{employee}/leave-days', [EmployeeLeaveDayController::class, 'index'])->name('employees.leave-days.index');
    Route::post('employees/{employee}/leave-days', [EmployeeLeaveDayController::class, 'store'])->name('employees.leave-days.store');
    Route::delete('employees/{employee}/leave-days/{leaveDay}', [EmployeeLeaveDayController::class, 'destroy'])->name('employees.leave-days.destroy');
    Route::put('employees/{employee}/schedule', [EmployeeLeaveDayController::class, 'updateSchedule'])->name('employees.schedule.update');
    Route::post('employees/salary/{id}', [EmployeeController::class, 'salary'])->name('employees.salary');
    Route::get('employees/salary/delete/{id}', [EmployeeController::class, 'salaryDelete'])->name('employees.salary.delete');
    Route::resource('employees', EmployeeController::class, ['names' => 'employees']);
    Route::resource('cost-categories', CostCategoryController::class, ['names' => 'cost_categories']);
    Route::resource('costs', CostController::class, ['names' => 'costs']);

    Route::get('/admin/employees/{id}/after-cost', [EmployeeController::class, 'getAfterCost']);

    Route::post('/costs/multiple/store', [CostController::class, 'storeMultiple'])->name('cost.store-multiple');
    Route::resource('earns', EarnController::class, ['names' => 'earns']);
    Route::resource('items', ItemController::class, ['names' => 'items']);
    Route::resource('purchases', PurchaseController::class, ['names' => 'purchases']);
    Route::get('/purchases/items/data', [PurchaseController::class, 'items'])->name('items.purchases');
    Route::get('purchases/edit-item/{id}', [PurchaseController::class, 'editItem'])->name('purchases.edit-item');
    Route::post('purchases/payment', [PurchaseController::class, 'purchasePayment'])->name('purchases.payment');
    Route::post('purchases/update-item/{id}', [PurchaseController::class, 'updateItem'])->name('purchases.update-item');
    Route::resource('purchase_items', PurchaseItemController::class, ['names' => 'purchase_items']);
    Route::resource('test_reports', TestReportController::class, ['names' => 'test_reports']);
    Route::resource('test_report_demos', TestReportDemoController::class, ['names' => 'test_report_demos']);
    Route::get('test-report-preview/{id}', [TestReportController::class, 'reportPdfPreview'])->name('preview-pdf-report');
    Route::get('test-report-delete/{id}', [TestReportController::class, 'reportPdfdelete'])->name('preview-pdf-delete');
    Route::resource('number-categories', NumberCategoryController::class, ['names' => 'number_categories']);
    Route::resource('phone-numbers', PhoneNumberController::class, ['names' => 'phone_numbers']);
    Route::post('phone-number-upload', [PhoneNumberController::class, 'phoneNumberUpload'])->name('phone_numbers-upload');
    Route::post('phone-number-message', [PhoneNumberController::class, 'phoneNumberSend'])->name('phone_numbers-message');


    //Route::get('purchases/{purchaseItemId}/edititem', [PurchaseController::class, 'editItem'])->name('admin.purchases.edititem');
    // Route::put('purchases/{purchaseItemId}/updateitem', [PurchaseController::class, 'updateItem'])->name('admin.purchases.updateitem');


    //Route::get('/purchases/edititems/{id}', [PurchaseController::class, 'edititems'])->name('admin.purchases.edititems');


    Route::resource('payments', PaymentController::class, ['names' => 'payments']);
    Route::resource('suppliers', SupplierController::class, ['names' => 'suppliers']);
    Route::resource('labs', LabController::class, ['names' => 'labs']);
    Route::get('/labs/status/{id}', [LabController::class, 'labTestStatus'])->name('lab.test.status');
    Route::post('labs/update-item/{invoiceItemId}', [LabController::class, 'updateItem'])->name('lab.update-item');
    Route::get('labs/reagent-delete/{reagent_track_id}', [LabController::class, 'DeleteReagetntTrack'])->name('lab.delete-reagetnt-track');

    Route::get('doctor-serials/next-serial/{reefer}', [DoctorSerialController::class, 'nextSerialAjax'])->name('doctor_serials.next');
    Route::resource('doctor-serials', DoctorSerialController::class, ['names' => 'doctor_serials']);
    Route::resource('doctor-rooms', DoctorRoomController::class, ['names' => 'doctor_rooms']);
    Route::resource('prescriptions', PrescriptionController::class);
    Route::post('/prescriptions/store', [PrescriptionController::class, 'store'])->name('prescriptions.store');


    Route::get('/reports/collections', [ReportController::class, 'collections'])->name('reports.collections');
    Route::get('/reports/recept-collections', [ReportController::class, 'hospitalCollections'])->name('reports.recept-collections');
    Route::get('/reports/balance', [ReportController::class, 'balance'])->name('reports.balance');
    Route::get('/reports/balance-day-wise', [ReportController::class, 'balanceDayWise'])->name('reports.balance-day-wise');
    Route::get('/reports/categories', [ReportController::class, 'categories'])->name('reports.categories');
    Route::get('/reports/references', [ReportController::class, 'references'])->name('reports.references');
    Route::get('/reports/references/payment', [ReportController::class, 'referencesPayment'])->name('reports.references.payment');
    Route::get('/reports/references/doctor', [ReportController::class, 'referencesDoctor'])->name('reports.references.doctor');
    Route::get('/reports/costs', [ReportController::class, 'cost'])->name('reports.costs');
    Route::get('/reports/costs/pdf', [ReportController::class, 'costPdf'])->name('costs.report-pdf');
    Route::get('/reports/costs/category/pdf', [ReportController::class, 'costCategoryPdf'])->name('costs.report-category-pdf');
    Route::get('/reports/costs/category/specific/pdf', [ReportController::class, 'costCategoryIdPdf'])->name('costs.report-category-pdf-id');
    Route::get('/reports/pharmacy-stock', [ReportController::class, 'pharmacyStock'])->name('reports.pharmacy-stock');

//


//    API FOR AJAX

    Route::get('/get-products', [ApiController::class, 'getProducts']);
    Route::get('/get-pharmacy-products', [ApiController::class, 'getPharmacyProducts']);
    Route::get('/get-services', [ApiController::class, 'getServices']);
    Route::get('/get-doctors', [ApiController::class, 'getDoctors']);
    Route::get('/get-referrals', [ApiController::class, 'getReefs']);
    Route::get('/search-phone', [ApiController::class, 'searchUserPhone']);
    Route::post('/create-user-api', [ApiController::class, 'storeUser'])->name('users.store.api');
    Route::get('/get-services-by-category/{id}', [ApiController::class, 'getByCategory']);
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendance/{attendance}/time', [AttendanceController::class, 'updateTime'])->name('attendance.update-time');

});
Route::get('/checkDevice', [SerialController::class, 'checkDevice']);
Route::get('/attendance-api/{branch_id}/{rfid}', [ApiController::class, 'attendanceStore'])->name('attendance.api.store');



// Fingerprint
Route::post('/fingerprint-send', [FingerprintController::class, 'send'])->name('fingerprint.send');
Route::get('/fingerprint-show', [FingerprintController::class, 'show'])->name('fingerprint.show');
Route::get('/fingerprint-check', [FingerprintController::class, 'check'])->name('fingerprint.check');

// Attendance (mark in/out)
Route::post('/attendance/mark', [AttendanceController::class, 'mark']);
// AJAX route for RFID uniqueness check

require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\AppDownloadController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SupplyItemController;
use App\Http\Controllers\ProductRecipeController;
use App\Http\Controllers\StockOpnameController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

// Public APK download so a user can install the app before ever logging in.
Route::get('/download/app', [AppDownloadController::class, 'download'])->name('app.download');
Route::get('/api/app/version', [AppDownloadController::class, 'version'])->name('app.version');

Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout')->middleware('auth');

Route::middleware(['auth', 'permission:access_cashier'])->controller(LoginController::class)->group(function() {
    Route::get('/dashboard', 'index')->name('auth.index');
});

// Admin Dashboard
Route::middleware('permission:access_admin_dashboard')->controller(DashboardController::class)->group(function() {
    Route::get('/admin/dashboard', 'index')->name('admin.dashboard');
});

// FCM device token registration (any logged-in role — admin, cashier, kitchen).
// Which of these tokens actually receive pushes is decided in FcmService, not here.
Route::middleware('auth')->controller(FcmTokenController::class)->group(function() {
    Route::post('/fcm/token', 'store')->name('fcm.token.store');
});

// Unregistering a token intentionally does NOT require auth: a device must be able to stop
// receiving pushes even when its session has already expired or been force-logged-out (it
// can no longer hit an `auth`-gated endpoint at that point). Deleting by exact token value
// is low-risk — it only removes a registration, never reveals or reads anything.
Route::delete('/fcm/token', [FcmTokenController::class, 'destroy'])->name('fcm.token.destroy');

Route::resource('products',ProductsController::class)->middleware('permission:manage_products')->except('show');
Route::resource('products',ProductsController::class)->middleware('auth')->only('show'); 
Route::middleware('permission:manage_products')->controller(ProductsController::class)->group(function() {
    Route::post('/{uuid}/active','activeToggle')->name('products.active');
});
Route::resource('categories',CategoriesController::class)->middleware('permission:manage_categories')->except(['show']);

Route::middleware('permission:manage_settings')->controller(SettingsController::class)->group(function() {
    Route::get('/settings','index')->name('settings.index');
    Route::post('/settings/table/create','tableCreate')->name('settings.table.create');
    Route::post('/settings/table/sort','tableSort')->name('settings.table.sort');
    Route::post('/settings/category/sort','categorySort')->name('settings.category.sort');
    Route::delete('/settings/table/{uuid}/delete','tableDelete')->name('settings.table.delete');
    Route::post('/settings/payment/tax/update','paymentTaxUpdate')->name('settings.payment.tax.update');
    Route::post('/settings/restaurant/update','restaurantUpdate')->name('settings.restaurant.update');
    Route::post('/settings/attendance/late','attendanceLateUpdate')->name('settings.attendance.late.update');
    Route::post('/settings/notification/daily-revenue','dailyRevenueNotificationUpdate')->name('settings.notification.daily-revenue.update');
    Route::post('/settings/apk/upload','apkUpload')->name('settings.apk.upload');
    Route::delete('/settings/apk/delete','apkDelete')->name('settings.apk.delete');
});

Route::middleware('permission:manage_cashflow')->controller(CashFlowController::class)->group(function() {
    Route::get('/cashflow', 'index')->name('cashflow.index');
    Route::post('/cashflow/accounts', 'storeAccount')->name('cashflow.accounts.store');
    Route::put('/cashflow/accounts/{uuid}', 'updateAccount')->name('cashflow.accounts.update');
    Route::delete('/cashflow/accounts/{uuid}', 'destroyAccount')->name('cashflow.accounts.destroy');
    Route::post('/cashflow/transactions', 'storeTransaction')->name('cashflow.transactions.store');
    Route::put('/cashflow/transactions/{uuid}', 'updateTransaction')->name('cashflow.transactions.update');
    Route::delete('/cashflow/transactions/{uuid}', 'destroyTransaction')->name('cashflow.transactions.destroy');
    Route::get('/cashflow/reconciliation/sales-data', 'getSalesDataByDate')->name('cashflow.reconciliation.sales-data');
    Route::post('/cashflow/categories', 'storeCategory')->name('cashflow.categories.store');
    Route::delete('/cashflow/categories/{uuid}', 'destroyCategory')->name('cashflow.categories.destroy');
});

Route::middleware('permission:access_cashier')->controller(TransactionsController::class)->group(function(){
    Route::get('/transaction/live-updates','getLiveUpdates')->name('transaction.live-updates');
    Route::post('/transaction/create','create')->name('transaction.create');
    Route::get('/transaction/{uuid}/show','show')->name('transaction.show');
    Route::delete('/transaction/{uuid}/delete','delete')->name('transaction.delete');
    Route::post('/transaction/{uuid}/update','update')->name('transaction.update');
    Route::post('/transaction/order/{uuid}/create','createOrder')->name('transaction.order.create');
    Route::post('/transaction/order/{uuid}/increment','incrementOrder')->name('transaction.order.increment');
    Route::post('/transaction/order/{uuid}/decrement','decrementOrder')->name('transaction.order.decrement');
    Route::post('/transaction/order/{uuid}/changeQty','changeQtyOrder')->name('transaction.order.changeQty');
    Route::post('/transaction/order/{uuid}/changeTable','changeTableOrder')->name('transaction.order.changeTable');
    Route::post('/transaction/order/{uuid}/changeOrder','changeOrderType')->name('transaction.order.changeOrderType');
    Route::post('/transaction/order/{uuid}/changeName','changeNameOrder')->name('transaction.order.changeName');
    Route::get('/transaction/order/{uuid}/getNote','getNoteOrder')->name('transaction.order.getNote');
    Route::post('/transaction/order/{uuid}/changeNote','changeNoteOrder')->name('transaction.order.changeNote');
    Route::post('/transaction/order/{uuid}/changeDetail','changeOrderDetail')->name('transaction.order.changeDetail');
    Route::delete('/transaction/order/{uuid}/deleteOrder','deleteOrder')->name('transaction.order.delete');
    Route::post('/transaction/{uuid}/submit','submitTransaction')->name('transaction.submit');
    Route::get('/transaction/{uuid}/payment','paymentTransaction')->name('transaction.payment');
    Route::post('/transaction/{uuid}/payment/discount','paymentTransactionDiscount')->name('transaction.payment.discount');
    Route::get('/transaction/{uuid}/payment/receipt/noprice','printCheckReceiptNoPrice')->name('transaction.print.check.noprice');
    Route::get('/transaction/{uuid}/payment/receipt/check','printCheckReceipt')->name('transaction.print.check');
    Route::post('/transaction/{uuid}/payment','proceedPaymentTransaction')->name('transaction.payment.proceed');
    Route::post('/transaction/{uuid}/payment/finalize','finalizePayment')->name('transaction.payment.finalize');
    Route::get('/transaction/{uuid}/payment/receipt','printReceipt')->name('transaction.print.payment');
});

// Pengajuan belanja stok & stock opname — cashier/kitchen can submit these day-to-day,
// admin approves purchases into stock and can delete history.
Route::middleware('permission:manage_stock')->group(function() {
    Route::get('/purchase-request',[PurchaseRequestController::class,'index'])->name('purchase-request.index');
    Route::post('/purchase-request',[PurchaseRequestController::class,'store'])->name('purchase-request.store');

    Route::get('/stock-opname',[StockOpnameController::class,'index'])->name('stock-opname.index');
    Route::post('/stock-opname',[StockOpnameController::class,'store'])->name('stock-opname.store');
});

// Approving a request adds stock, and editing master supplies changes what can be requested —
// both are admin-only on purpose.
Route::middleware('permission:access_admin_dashboard')->group(function() {
    Route::post('/purchase-request/{uuid}/purchased',[PurchaseRequestController::class,'markPurchased'])->name('purchase-request.purchased');
    Route::post('/purchase-request/{uuid}/cancel',[PurchaseRequestController::class,'cancel'])->name('purchase-request.cancel');
    Route::delete('/purchase-request/{uuid}',[PurchaseRequestController::class,'destroy'])->name('purchase-request.destroy');

    Route::delete('/stock-opname/{uuid}',[StockOpnameController::class,'destroy'])->name('stock-opname.destroy');

    Route::get('/product-recipe',[ProductRecipeController::class,'index'])->name('product-recipe.index');
    Route::post('/product-recipe',[ProductRecipeController::class,'store'])->name('product-recipe.store');
    Route::put('/product-recipe/{uuid}',[ProductRecipeController::class,'update'])->name('product-recipe.update');
    Route::delete('/product-recipe/{uuid}',[ProductRecipeController::class,'destroy'])->name('product-recipe.destroy');

    Route::get('/supply-item',[SupplyItemController::class,'index'])->name('supply-item.index');
    Route::post('/supply-item',[SupplyItemController::class,'store'])->name('supply-item.store');
    Route::put('/supply-item/{uuid}',[SupplyItemController::class,'update'])->name('supply-item.update');
    Route::post('/supply-item/{uuid}/toggle',[SupplyItemController::class,'toggleActive'])->name('supply-item.toggle');
    Route::delete('/supply-item/{uuid}',[SupplyItemController::class,'destroy'])->name('supply-item.destroy');
});

Route::middleware('permission:view_reports')->controller(ActivityController::class)->group(function() {
    Route::get('/activity','index')->name('activity.index');
    Route::get('/activity/history','history')->name('activity.history');
    Route::get('/activity/report','report')->name('activity.report');
    Route::get('/activity/report/export','exportExcel')->name('activity.report.export');
    Route::get('/activity/{date}/report','reportShow')->name('activity.report.show');
});

Route::resource('users',UserController::class)->middleware('permission:manage_users');
Route::middleware('permission:manage_users')->controller(UserController::class)->group(function() {
    Route::post('/users/{uuid}/reset','resetPassword')->name('users.reset');
});

Route::middleware('permission:manage_users')->controller(RoleController::class)->group(function() {
    Route::get('/roles', 'index')->name('roles.index');
    Route::post('/roles', 'store')->name('roles.store');
    Route::put('/roles/{uuid}', 'update')->name('roles.update');
    Route::delete('/roles/{uuid}', 'destroy')->name('roles.destroy');
});

Route::middleware('permission:view_kitchen_queue')->controller(KitchenController::class)->group(function() {
    Route::get('/kitchen/queue', 'index')->name('kitchen.queue');
    Route::get('/kitchen/queue/live-updates', 'getLiveUpdates')->name('kitchen.live-updates');
    Route::post('/kitchen/queue/{uuid}/status', 'updateStatus')->name('kitchen.status.update');
    Route::post('/kitchen/queue/item/{uuid}/status', 'updateItemStatus')->name('kitchen.item.status.update');
});

Route::middleware('auth')->controller(AttendanceController::class)->group(function() {
    Route::get('/attendance/today', 'checkToday')->name('attendance.today');
    Route::post('/attendance/clock-in', 'clockIn')->name('attendance.clock-in');
    Route::post('/attendance/clock-out', 'clockOut')->name('attendance.clock-out');
});

Route::middleware('auth')->group(function() {
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('users.update-profile');
});

Route::middleware('permission:manage_attendance')->controller(AttendanceController::class)->group(function() {
    Route::get('/attendance/recap', 'recapIndex')->name('attendance.recap');
    Route::get('/attendance/export', 'exportExcel')->name('attendance.export');
});

Route::middleware('permission:manage_attendance')->controller(PayrollController::class)->group(function() {
    Route::get('/payroll', 'index')->name('payroll.index');
    Route::post('/payroll/users/{uuid}/salary', 'updateDailySalary')->name('payroll.salary.update');
    Route::post('/payroll/adjustments', 'storeAdjustment')->name('payroll.adjustments.store');
    Route::delete('/payroll/adjustments/{uuid}', 'destroyAdjustment')->name('payroll.adjustments.destroy');
    Route::get('/payroll/print/{uuid}', 'printPayslip')->name('payroll.print');
    Route::get('/payroll/users/{uuid}/calendar-data', 'getCalendarData')->name('payroll.calendar-data');
    Route::post('/payroll/payout', 'storePayout')->name('payroll.payout.store');
    Route::delete('/payroll/payout/{uuid}', 'destroyPayout')->name('payroll.payout.destroy');
});

// Customer Self-Ordering Routes
Route::controller(CustomerOrderController::class)->group(function() {
    Route::get('/order/table/{table_uuid}', 'index')->name('customer.order.table');
    Route::post('/order/table/{table_uuid}/submit', 'submit')->name('customer.order.submit');
    Route::get('/order/status/{transaction_uuid}', 'status')->name('customer.order.status');
    Route::get('/order/status/{transaction_uuid}/live', 'liveStatus')->name('customer.order.liveStatus');
});

<?php

use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocController as AdminDocController;
use App\Http\Controllers\Admin\MerchantController as AdminMerchantController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicVoucherController;
use App\Http\Controllers\Umkm\ClaimController as UmkmClaimController;
use App\Http\Controllers\Umkm\PosController as UmkmPosController;
use App\Http\Controllers\Umkm\ProductController as UmkmProductController;
use App\Http\Controllers\Umkm\SaleController as UmkmSaleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public & Home Redirection
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('umkm.pos');
})->name('home');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Voucher View (for employee opening link in WA)
Route::get('/voucher/{code}', [PublicVoucherController::class, 'show'])->name('voucher.public');

// MPWA WhatsApp Webhook API
Route::any('/api/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->name('api.whatsapp.webhook');
Route::any('/api/webhook', [WhatsAppWebhookController::class, 'handle']);
Route::any('/webhook', [WhatsAppWebhookController::class, 'handle']);
Route::get('/api/whatsapp/status', [WhatsAppWebhookController::class, 'verify'])->name('api.whatsapp.status');

// Shortcuts
Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'));
Route::get('/pos', fn() => redirect()->route('umkm.pos'));
Route::get('/dokumentasi', fn() => redirect()->route('admin.docs.index'));

// ADMIN ROUTES
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Barcode & Voucher Management
    Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers/single', [AdminVoucherController::class, 'storeSingle'])->name('vouchers.single');
    Route::post('/vouchers/bulk', [AdminVoucherController::class, 'storeBulk'])->name('vouchers.bulk');
    Route::post('/vouchers/{id}/resend-wa', [AdminVoucherController::class, 'resendWhatsApp'])->name('vouchers.resend-wa');
    Route::post('/vouchers/{id}/activate', [AdminVoucherController::class, 'activateManual'])->name('vouchers.activate');
    Route::get('/vouchers/template', [AdminVoucherController::class, 'downloadTemplate'])->name('vouchers.template');

    // Claims Management
    Route::get('/claims', [AdminClaimController::class, 'index'])->name('claims.index');
    Route::get('/claims/{id}', [AdminClaimController::class, 'show'])->name('claims.show');
    Route::post('/claims/{id}/approve', [AdminClaimController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{id}/pay', [AdminClaimController::class, 'markAsPaid'])->name('claims.pay');
    Route::post('/claims/{id}/reject', [AdminClaimController::class, 'reject'])->name('claims.reject');
    Route::get('/claims/{id}/invoice', [AdminClaimController::class, 'printInvoice'])->name('claims.invoice');

    // Merchant (UMKM) Management
    Route::resource('merchants', AdminMerchantController::class)->except(['create', 'show', 'edit']);

    // Reports & Economic Turnover
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'exportExcel'])->name('reports.export');

    // Comprehensive Documentation
    Route::get('/docs', [AdminDocController::class, 'index'])->name('docs.index');
});

// UMKM ROUTES
Route::prefix('umkm')->name('umkm.')->middleware(['auth', 'role.umkm'])->group(function () {
    // POS Cashier & Scanner
    Route::get('/pos', [UmkmPosController::class, 'index'])->name('pos');
    Route::post('/pos/check-voucher', [UmkmPosController::class, 'checkVoucher'])->name('pos.check-voucher');
    Route::post('/pos/checkout', [UmkmPosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{id}', [UmkmPosController::class, 'receipt'])->name('pos.receipt');

    // Product & Stock Management
    Route::resource('products', UmkmProductController::class)->except(['create', 'show', 'edit']);
    Route::post('/products/{id}/stock', [UmkmProductController::class, 'updateStock'])->name('products.stock');

    // Sales History
    Route::get('/sales', [UmkmSaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{id}', [UmkmSaleController::class, 'show'])->name('sales.show');

    // Claim Reimbursement to Company
    Route::get('/claims', [UmkmClaimController::class, 'index'])->name('claims.index');
    Route::post('/claims', [UmkmClaimController::class, 'store'])->name('claims.store');
    Route::get('/claims/{id}/invoice', [UmkmClaimController::class, 'invoice'])->name('claims.invoice');
});

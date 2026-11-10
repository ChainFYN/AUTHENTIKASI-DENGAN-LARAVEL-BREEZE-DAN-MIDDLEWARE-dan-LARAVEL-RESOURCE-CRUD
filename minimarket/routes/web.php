<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ImageManipulationController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KopiKitaController;
use App\Http\Controllers\LaporanMinggu9Controller;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PdfReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('kasir.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('products', ProductController::class);
    });

    // Khusus Kasir
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    });

    // Admin + Kasir
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{id}/print-thermal', [TransactionController::class, 'printThermalReceipt'])->name('transactions.print.thermal');
        Route::get('/api/products/search', [TransactionController::class, 'searchProduct'])->name('api.products.search');
        Route::get('/api/customers', [TransactionController::class, 'getCustomers'])->name('api.customers');
    });

    // Profile routes (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // File Storage routes
    Route::get('/file-upload', [FileUploadController::class, 'showForm'])->name('file.form');
    Route::post('/file-upload', [FileUploadController::class, 'upload'])->name('file.upload');
    Route::post('/file-upload-custom', [FileUploadController::class, 'uploadCustomName'])->name('file.upload.custom');
    Route::get('/files', [FileUploadController::class, 'listFiles'])->name('file.list');
    Route::delete('/files/{filename}', [FileUploadController::class, 'deleteFile'])->name('file.delete');

    // PDF Report routes
    Route::get('/report/preview', [PdfReportController::class, 'previewReport'])->name('report.preview');
    Route::get('/report/download', [PdfReportController::class, 'generateReport'])->name('report.download');

    // Image Manipulation routes
    Route::get('/image-manipulation', [ImageManipulationController::class, 'showForm'])->name('image.form');
    Route::post('/image/resize', [ImageManipulationController::class, 'resize'])->name('image.resize');
    Route::post('/image/crop', [ImageManipulationController::class, 'crop'])->name('image.crop');
    Route::post('/image/rotate', [ImageManipulationController::class, 'rotate'])->name('image.rotate');
    Route::get('/images', [ImageManipulationController::class, 'listImages'])->name('image.list');
    Route::delete('/images/{filename}', [ImageManipulationController::class, 'deleteImage'])->name('image.delete');

    // Email routes
    Route::get('/email', [EmailController::class, 'showForm'])->name('email.form');
    Route::post('/email/send-welcome', [EmailController::class, 'sendWelcome'])->name('email.send.welcome');
    Route::post('/email/send-notification', [EmailController::class, 'sendNotification'])->name('email.send.notification');
    Route::get('/email/preview/welcome', [EmailController::class, 'previewWelcome'])->name('email.preview.welcome');
    Route::get('/email/preview/notification', [EmailController::class, 'previewNotification'])->name('email.preview.notification');

    // Laporan Minggu 9
    Route::get('/laporan-minggu-9/preview', [LaporanMinggu9Controller::class, 'previewPDF'])->name('laporan.minggu9.preview');
    Route::get('/laporan-minggu-9/download', [LaporanMinggu9Controller::class, 'generatePDF'])->name('laporan.minggu9.download');
});

// Acara 35: Laravel Print (ESC/POS)
Route::get('/print-receipt', [ReceiptController::class, 'print'])->name('print.receipt');
Route::get('/print-receipt/custom', [ReceiptController::class, 'customFormatting'])->name('print.receipt.custom');

// Acara 36: Studi Kasus Toko Kopi Kita (Auto Print ESC/POS)
Route::get('/kopi-kita/print', [KopiKitaController::class, 'printSample'])->name('kopikita.print');
Route::post('/kopi-kita/checkout', [KopiKitaController::class, 'checkoutAndPrint'])->name('kopikita.checkout');

// Acara 37 & 38: Payment Gateway Midtrans (Checkout Snap & Webhook Notification)
Route::get('/digital-products', [MidtransController::class, 'index'])->name('digital.products');
Route::post('/checkout', [MidtransController::class, 'checkout'])->name('midtrans.checkout');
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');
Route::get('/orders/{orderId}/status', [MidtransController::class, 'orderStatus'])->name('orders.status');

require __DIR__.'/auth.php';

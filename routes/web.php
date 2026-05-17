<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InventoryAIController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\MfaController;

Route::get('/', function () {
    return redirect()->route('login');
});

// MFA Routes — outside auth middleware
Route::get('/mfa/verify', [MfaController::class, 'show'])->name('mfa.verify');
Route::post('/mfa/verify', [MfaController::class, 'verify'])->name('mfa.verify.submit');
Route::post('/mfa/resend', [MfaController::class, 'resend'])->name('mfa.resend');

Route::middleware(['auth', 'verified', 'check.status'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // MFA toggle
    Route::post('/mfa/toggle', [MfaController::class, 'toggle'])->name('mfa.toggle');

    // Menu Management
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [MenuCategoryController::class, 'index'])->name('index');
        Route::get('/categories/create', [MenuCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [MenuCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{menuCategory}/edit', [MenuCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{menuCategory}', [MenuCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{menuCategory}', [MenuCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/items/create', [MenuItemController::class, 'create'])->name('items.create');
        Route::post('/items', [MenuItemController::class, 'store'])->name('items.store');
        Route::get('/items/{menuItem}', [MenuItemController::class, 'show'])->name('items.show');
        Route::get('/items/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{menuItem}', [MenuItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{menuItem}', [MenuItemController::class, 'destroy'])->name('items.destroy');
        Route::patch('/items/{menuItem}/toggle', [MenuItemController::class, 'toggle'])->name('items.toggle');
    });

    // Tables
    Route::prefix('tables')->name('tables.')->group(function () {
        Route::get('/', [TableController::class, 'index'])->name('index');
        Route::get('/create', [TableController::class, 'create'])->name('create');
        Route::post('/', [TableController::class, 'store'])->name('store');
        Route::get('/{table}/edit', [TableController::class, 'edit'])->name('edit');
        Route::put('/{table}', [TableController::class, 'update'])->name('update');
        Route::delete('/{table}', [TableController::class, 'destroy'])->name('destroy');
        Route::patch('/{table}/status', [TableController::class, 'updateStatus'])->name('status');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('status');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        Route::post('/{order}/items', [OrderController::class, 'addItem'])->name('items.add');
        Route::delete('/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('items.remove');
        Route::post('/{order}/bill', [OrderController::class, 'generateBill'])->name('bill');
        Route::post('/{order}/payment', [OrderController::class, 'processPayment'])->name('payment');
    });

    // Kitchen Display
    Route::prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('index');
        Route::patch('/items/{item}/status', [KitchenController::class, 'updateItemStatus'])->name('items.status');
    });

    // Reservations
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/create', [ReservationController::class, 'create'])->name('create');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('edit');
        Route::put('/{reservation}', [ReservationController::class, 'update'])->name('update');
        Route::patch('/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('status');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('destroy');
    });

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventoryItem}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventoryItem}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventoryItem}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventoryItem}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::post('/{inventoryItem}/adjust', [InventoryController::class, 'adjust'])->name('adjust');

        // Suppliers
        Route::get('/suppliers/list', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/status', [UserController::class, 'updateStatus'])->name('status');
        Route::post('/{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
        Route::post('/stop-impersonating', [UserController::class, 'stopImpersonating'])->name('stop-impersonating');
        Route::post('/{user}/force-logout', [UserController::class, 'forceLogout'])->name('force-logout');
    });

    // Audit Logs
    Route::prefix('audit-logs')->name('audit-logs.')->middleware('role:admin|manager')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
        Route::delete('/{auditLog}', [AuditLogController::class, 'destroy'])->name('destroy');
        Route::post('/purge-old', [AuditLogController::class, 'purgeOld'])->name('purge');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
        Route::get('/count', [NotificationController::class, 'unreadCount'])->name('count');
        Route::get('/feed', [NotificationController::class, 'feed'])->name('feed');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'exportCsv'])->name('export');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->middleware('can:view settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
    });

    // Import
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::get('/template/menu', [ImportController::class, 'downloadMenuTemplate'])->name('template.menu');
        Route::get('/template/inventory', [ImportController::class, 'downloadInventoryTemplate'])->name('template.inventory');
        Route::post('/menu', [ImportController::class, 'importMenu'])->name('menu');
        Route::post('/inventory', [ImportController::class, 'importInventory'])->name('inventory');
    });

    // Inventory AI
    Route::post('/inventory/{inventoryItem}/ai-suggest', [InventoryAIController::class, 'suggest'])
        ->name('inventory.ai.suggest');
    Route::patch('/inventory/ai/suggestions/{suggestion}/acted', [InventoryAIController::class, 'markActedOn'])
        ->name('inventory.ai.acted');
    Route::get('/inventory/ai/dashboard', [InventoryAIController::class, 'dashboard'])
        ->name('inventory.ai.dashboard');

    // Backup System
    Route::prefix('backups')->name('backups.')->middleware('role:admin|manager')->group(function () {
        Route::get('/',                   [BackupController::class, 'index'])->name('index');
        Route::post('/',                  [BackupController::class, 'store'])->name('store');
        Route::get('/{backup}/download',  [BackupController::class, 'download'])->name('download');
        Route::post('/{backup}/verify',   [BackupController::class, 'verify'])->name('verify');
        Route::delete('/{backup}',        [BackupController::class, 'destroy'])->name('destroy');
    });

    // PDF Generation
    Route::prefix('pdf')->name('pdf.')->group(function () {
        Route::get('/orders/{order}/receipt',  [PdfController::class, 'orderReceipt'])->name('order.receipt');
        Route::get('/reports/sales',           [PdfController::class, 'salesReport'])->name('reports.sales');
        Route::get('/reports/inventory',       [PdfController::class, 'inventoryReport'])->name('reports.inventory');
    });

});

require __DIR__.'/auth.php';
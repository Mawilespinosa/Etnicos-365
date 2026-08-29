<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\PublicStoreController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // Users (RBAC)
    Route::middleware('permission:users.view')->get('/users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:users.create')->get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::middleware('permission:users.create')->post('/users', [UserController::class, 'store'])->name('users.store');
    Route::middleware('permission:users.update')->get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::middleware('permission:users.update')->put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::middleware('permission:users.delete')->delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Roles (RBAC)
    Route::middleware('permission:roles.view')->get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::middleware('permission:roles.create')->get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::middleware('permission:roles.create')->post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::middleware('permission:roles.update')->get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::middleware('permission:roles.update')->put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::middleware('permission:roles.delete')->delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Sellers (catalog)
    Route::resource('sellers', SellerController::class)
        ->middleware('auth')
        ->middlewareFor(['index', 'show'], 'permission:sellers.view')
        ->middlewareFor(['create', 'store'], 'permission:sellers.create')
        ->middlewareFor(['edit', 'update'], 'permission:sellers.update')
        ->middlewareFor('destroy', 'permission:sellers.delete');

    // Clients (catalog)
    Route::resource('clients', ClientController::class)
        ->middleware('auth')
        ->middlewareFor(['index', 'show'], 'permission:clients.view')
        ->middlewareFor(['create', 'store'], 'permission:clients.create')
        ->middlewareFor(['edit', 'update'], 'permission:clients.update')
        ->middlewareFor('destroy', 'permission:clients.delete');

    // Suppliers (catalog)
    Route::resource('suppliers', SupplierController::class)
        ->middleware('auth')
        ->middlewareFor(['index', 'show'], 'permission:suppliers.view')
        ->middlewareFor(['create', 'store'], 'permission:suppliers.create')
        ->middlewareFor(['edit', 'update'], 'permission:suppliers.update')
        ->middlewareFor('destroy', 'permission:suppliers.delete');

    // Products (catalog) + BOM management
    Route::resource('products', ProductController::class)
        ->middleware('auth')
        ->middlewareFor(['index', 'show'], 'permission:products.view')
        ->middlewareFor(['create', 'store'], 'permission:products.create')
        ->middlewareFor(['edit', 'update'], 'permission:products.update')
        ->middlewareFor('destroy', 'permission:products.delete');

    Route::middleware('permission:bill_of_materials.create')
        ->post('/products/{product}/materials', [ProductController::class, 'addMaterial'])
        ->name('products.materials.store');
    Route::middleware('permission:bill_of_materials.delete')
        ->delete('/products/{product}/materials/{material}', [ProductController::class, 'removeMaterial'])
        ->name('products.materials.destroy');

    // Raw materials (catalog)
    Route::resource('raw-materials', RawMaterialController::class)
        ->middleware('auth')
        ->middlewareFor(['index', 'show'], 'permission:raw_materials.view')
        ->middlewareFor(['create', 'store'], 'permission:raw_materials.create')
        ->middlewareFor(['edit', 'update'], 'permission:raw_materials.update')
        ->middlewareFor('destroy', 'permission:raw_materials.delete');

    // Production orders (OT + 8 fixed stages)
    Route::middleware('permission:production.view')->get('/production/orders', [ProductionOrderController::class, 'index'])->name('production.orders.index');
    Route::middleware('permission:production.create')->get('/production/orders/create', [ProductionOrderController::class, 'create'])->name('production.orders.create');
    Route::middleware('permission:production.create')->post('/production/orders', [ProductionOrderController::class, 'store'])->name('production.orders.store');
    Route::middleware('permission:production.view')->get('/production/orders/{order}', [ProductionOrderController::class, 'show'])->name('production.orders.show');
    Route::middleware('permission:production.update')->get('/production/orders/{order}/edit', [ProductionOrderController::class, 'edit'])->name('production.orders.edit');
    Route::middleware('permission:production.update')->put('/production/orders/{order}', [ProductionOrderController::class, 'update'])->name('production.orders.update');
    Route::middleware('permission:production.advance')->post('/production/orders/{order}/advance', [ProductionOrderController::class, 'advance'])
        ->middleware('throttle:20,1')
        ->name('production.orders.advance');
    Route::middleware('permission:production.update')->post('/production/orders/{order}/cancel', [ProductionOrderController::class, 'cancel'])
        ->middleware('throttle:20,1')
        ->name('production.orders.cancel');
    Route::middleware('permission:production.delete')->delete('/production/orders/{order}', [ProductionOrderController::class, 'destroy'])->name('production.orders.destroy');

    // Inventory
    Route::middleware('permission:inventory.view')->get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::middleware('permission:inventory.movements')->get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::middleware('permission:inventory.movements')->post('/inventory/movements', [InventoryController::class, 'storeMovement'])
        ->middleware('throttle:20,1')
        ->name('inventory.movements.store');
    Route::middleware('permission:inventory.view')->get('/inventory/alerts', [InventoryController::class, 'alerts'])->name('inventory.alerts');

    // Sales (invoicing: cash and credit)
    Route::middleware('permission:sales.view')->get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::middleware('permission:sales.create')->get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::middleware('permission:sales.create')->post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::middleware('permission:sales.view')->get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::middleware('permission:sales.update')->post('/sales/{sale}/confirm', [SaleController::class, 'confirm'])
        ->middleware('throttle:20,1')
        ->name('sales.confirm');
    Route::middleware('permission:sales.update')->post('/sales/{sale}/payments', [SaleController::class, 'addPayment'])
        ->middleware('throttle:20,1')
        ->name('sales.payments.store');
    Route::middleware('permission:sales.update')->post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])
        ->middleware('throttle:20,1')
        ->name('sales.cancel');
    Route::middleware('permission:sales.delete')->delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');

    // Finances (incomes, expenses, profit)
    Route::middleware('permission:finances.view')->get('/finances', [FinanceController::class, 'index'])->name('finances.index');
    Route::middleware('permission:finances.create')->post('/finances/incomes', [FinanceController::class, 'storeIncome'])
        ->middleware('throttle:20,1')
        ->name('finances.incomes.store');
    Route::middleware('permission:finances.create')->post('/finances/expenses', [FinanceController::class, 'storeExpense'])
        ->middleware('throttle:20,1')
        ->name('finances.expenses.store');
    Route::middleware('permission:finances.delete')->delete('/finances/incomes/{income}', [FinanceController::class, 'destroyIncome'])->name('finances.incomes.destroy');
    Route::middleware('permission:finances.delete')->delete('/finances/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->name('finances.expenses.destroy');

    // Reports (PDF / CSV)
    Route::middleware('permission:reports.export')->get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::middleware('permission:reports.export')->get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::middleware('permission:reports.export')->get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::middleware('permission:reports.export')->get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
});

// Public Store (Tienda pública) - No auth required
Route::prefix('tienda')->name('store.')->group(function () {
    Route::get('/', [PublicStoreController::class, 'index'])->name('index');
    Route::post('cart/add', [PublicStoreController::class, 'addToCart'])->name('cart.add');
    Route::get('cart', [PublicStoreController::class, 'cart'])->name('cart');
    Route::post('cart/update', [PublicStoreController::class, 'updateCart'])->name('cart.update');
    Route::get('checkout', [PublicStoreController::class, 'checkout'])->name('checkout');
    Route::post('checkout', [PublicStoreController::class, 'processCheckout'])->name('checkout.process');
    Route::get('pse/pay/{sale}', [PublicStoreController::class, 'psePay'])->name('pse.pay');
    Route::post('pse/callback', [PublicStoreController::class, 'pseCallback'])->name('pse.callback');
    Route::get('/{product}', [PublicStoreController::class, 'show'])->name('show');
});
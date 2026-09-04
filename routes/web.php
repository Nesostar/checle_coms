<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect homepage to login page
Route::get('/', [AuthController::class, 'login'])->name('login');

// Handle login form submission
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.submit');

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ===============================
// ADMIN ROUTES
// ===============================
Route::middleware(['auth'])->group(function () {
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('index');


    // Purchase
    Route::get('/purchase', [AdminController::class, 'purchase'])->name('purchase');


    // Deposit
    Route::get('/deposit', [AdminController::class, 'deposit'])->name('deposit');


    // =====================
// DASHBOARD ROUTE
// =====================
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('index');
    
});


   ///=============
// PURCHASE ROUTES
//=================
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::get('/', [AdminController::class, 'purchaseIndex'])->name('index');
        Route::post('/store', [AdminController::class, 'purchaseStore'])->name('store');
    });


    ///=================
// DEPOSIT ROUTES
    //==================
    Route::prefix('deposit')->name('deposit.')->group(function () {
        Route::get('/', [AdminController::class, 'depositIndex'])->name('index');
        Route::post('/store', [AdminController::class, 'depositStore'])->name('store');
    });
    
    
    // ===============================
// ITEMS MANAGEMENT
// ===============================
Route::prefix('items')->name('items.')->group(function () {

    Route::get('/category', [AdminController::class, 'itemCategory'])->name('category');
    Route::get('/subcategory', [AdminController::class, 'itemSubCategory'])->name('subcategory');
    Route::get('/name', [AdminController::class, 'itemName'])->name('name');
    Route::get('/entry-type', [AdminController::class, 'entryType'])->name('entryType');
    Route::get('/stock', [AdminController::class, 'inventory'])->name('stock');
    Route::get('/adjustment', [AdminController::class, 'stockAdjustment'])->name('adjustment');
    Route::get('/outofstock', [AdminController::class, 'outOfStock'])->name('outofstock');
    Route::get('/expired', [AdminController::class, 'expiredItems'])->name('expired');
});

// CATEGORY CRUD (ADMIN)
Route::prefix('categories')->name('categories.')->group(function () {

    Route::get('/', [AdminController::class, 'categoryIndex'])->name('index');
    Route::get('/create', [AdminController::class, 'categoryCreate'])->name('create');
    Route::post('/store', [AdminController::class, 'categoryStore'])->name('store');
    Route::get('/edit/{id}', [AdminController::class, 'categoryEdit'])->name('edit');
    Route::put('/update/{id}', [AdminController::class, 'categoryUpdate'])->name('update');
    Route::delete('/delete/{id}', [AdminController::class, 'categoryDestroy'])->name('destroy');
});


// SUBCATEGORY
Route::prefix('items')->name('items.')->group(function () {
    Route::prefix('subcategories')->name('subcategories.')->group(function () {
        Route::get('/', [AdminController::class, 'itemSubCategory'])->name('index');
        Route::post('/store', [AdminController::class, 'subcategoryStore'])->name('store');
        Route::get('/edit/{id}', [AdminController::class, 'subcategoryEdit'])->name('edit');
        Route::put('/update/{id}', [AdminController::class, 'subcategoryUpdate'])->name('update');
        Route::delete('/delete/{id}', [AdminController::class, 'subcategoryDestroy'])->name('destroy');
    });


   // ITEM NAME CRUD
Route::prefix('names')->name('names.')->group(function () {
    Route::get('/', [AdminController::class, 'itemName'])->name('index');
    Route::post('/store', [AdminController::class, 'itemNameStore'])->name('store');
    Route::get('/edit/{id}', [AdminController::class, 'itemNameEdit'])->name('edit');
    Route::put('/update/{id}', [AdminController::class, 'itemNameUpdate'])->name('update');
    Route::delete('/delete/{id}', [AdminController::class, 'itemNameDestroy'])->name('destroy');

});

// ENTRY TYPE CRUD
Route::prefix('entrytype')->name('entrytype.')->group(function () {
    Route::get('/', [AdminController::class, 'entryType'])->name('index');
    Route::post('/store', [AdminController::class, 'entryTypeStore'])->name('store');
    Route::put('/update/{id}', [AdminController::class, 'entryTypeUpdate'])->name('update');
    Route::delete('/delete/{id}', [AdminController::class, 'entryTypeDestroy'])->name('destroy');
});

Route::prefix('inventory')->name('inventory.')->group(function () {

    // Inventory main page
    Route::get('/', [AdminController::class, 'inventory'])->name('index');
    Route::post('/store', [AdminController::class, 'inventoryStore'])->name('store');
    Route::put('/update/{id}', [AdminController::class, 'inventoryUpdate'])->name('update');
    Route::delete('/delete/{id}', [AdminController::class, 'inventoryDestroy'])->name('destroy');

    // Stock Adjustment
    Route::get('/adjustment', [AdminController::class, 'stockAdjustment'])->name('adjust');
    Route::post('/adjustment/store', [AdminController::class, 'stockAdjustmentStore'])->name('adjust.store');

    // Out of Stock
    Route::get('/out-of-stock', [AdminController::class, 'outOfStock'])->name('out-of-stock');

    // Expired Items
    Route::get('/expired', [AdminController::class, 'expiredItems'])->name('expired');

    // Item Transactions / History
    Route::get('/transactions/{item}', [AdminController::class, 'itemTransactions'])->name('transactions');
});

});



    // ===============================
    // BILLING MANAGEMENT
    // ===============================
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/pos', [AdminController::class, 'pos'])->name('pos');
        Route::get('/paid-list', [AdminController::class, 'paidList'])->name('paidList');
        Route::get('/incomplete-payments', [AdminController::class, 'incompletePayments'])->name('incompletePayments');
    });

   // ===============================
// BILLING MANAGEMENT - POS
// ===============================
Route::prefix('billing')->name('billing.')->group(function () {
    Route::get('/pos', [AdminController::class, 'pos'])->name('pos'); // Show POS page
    Route::post('/pos/store', [AdminController::class, 'posStore'])->name('store'); // Handle POS form submission
});



    // ===============================
    // EXPENDITURE MANAGEMENT
    // ===============================
    Route::get('/expenditure/list', [AdminController::class, 'expenditureList'])->name('expenditure.list');
    Route::get('/expenditure/category', [AdminController::class, 'expenditureCategory'])->name('expenditure.category');

    // ===============================
// EXPENDITURE MANAGEMENT
// ===============================
Route::prefix('expenditure')->name('expenditure.')->group(function () {
    Route::get('/list', [AdminController::class, 'expenditureList'])->name('list');
    Route::post('/store', [AdminController::class, 'expenditureStore'])->name('store');

    // You can add edit, update, delete routes here as well
    Route::get('/edit/{id}', [AdminController::class, 'expenditureEdit'])->name('edit');
    Route::put('/update/{id}', [AdminController::class, 'expenditureUpdate'])->name('update');
    Route::delete('/delete/{id}', [AdminController::class, 'expenditureDestroy'])->name('destroy');
});

Route::prefix('expenditure')->name('expenditure.')->group(function () {
    Route::get('/category', [AdminController::class, 'expenditureCategory'])->name('category');
    Route::post('/category/store', [AdminController::class, 'expenditureCategoryStore'])->name('category.store');
    Route::put('/category/update/{id}', [AdminController::class, 'expenditureCategoryUpdate'])->name('category.update');
    Route::delete('/category/delete/{id}', [AdminController::class, 'expenditureCategoryDestroy'])->name('category.destroy');

    Route::get('/list', [AdminController::class, 'expenditureList'])->name('list');
    Route::post('/store', [AdminController::class, 'expenditureStore'])->name('store');
    Route::put('/update/{id}', [AdminController::class, 'expenditureUpdate'])->name('update');
    Route::delete('/delete/{id}', [AdminController::class, 'expenditureDestroy'])->name('destroy');
});


    // ===============================
// REPORT MANAGEMENT (FINAL)
// ===============================
Route::prefix('report')->name('report.')->group(function () {

    // SALES
    Route::get('/sales', [AdminController::class, 'salesReport'])->name('sales');
    Route::get('/sales/pdf', [AdminController::class, 'salesReportPdf'])->name('sales.pdf');

    // CASHBOOK
    Route::get('/cashbook', [AdminController::class, 'cashBookReport'])->name('cashbook');
    Route::get('/cashbook/pdf', [AdminController::class, 'cashBookPdf'])->name('cashbook.pdf');

    // INCOME
    Route::get('/income', [AdminController::class, 'incomeReport'])->name('income');
    Route::get('/income/pdf', [AdminController::class, 'incomeReportPdf'])->name('income.pdf');

});

    // ===============================
    // CUSTOMERS & SUPPLIERS
    // ===============================
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::get('/suppliers', [AdminController::class, 'suppliers'])->name('suppliers');
    Route::get('/quotation', [AdminController::class, 'quotation'])->name('quotation');
    Route::get('/invoice', [AdminController::class, 'invoice'])->name('invoice');


    // CUSTOMERS
Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [AdminController::class, 'customersIndex'])->name('index');
    Route::post('/store', [AdminController::class, 'customersStore'])->name('store');
    Route::post('/{id}/update', [AdminController::class, 'customersUpdate'])->name('update');
    Route::delete('/{id}/delete', [AdminController::class, 'customersDelete'])->name('delete');
});
    
    // SUPPLIERS
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/store', [AdminController::class, 'store'])->name('store');
        Route::post('/{id}/update', [AdminController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [AdminController::class, 'delete'])->name('delete');
    
    });
    
   
   // QUOTATIONS
   Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [AdminController::class, 'quotations'])->name('index');
    Route::post('/store', [AdminController::class, 'quotationStore'])->name('store');
    Route::get('/{id}/delete', [AdminController::class, 'quotationDelete'])->name('delete');
});

   // INVOICE
   Route::prefix('invoices')->name('invoices.')->group(function () {

    // Invoice main page (form + list)
    Route::get('/', [AdminController::class, 'invoices'])->name('index');

    // Create / Update invoice
    Route::post('/store', [AdminController::class, 'invoiceStore'])->name('store');

    // Download / Print PDF
    Route::get('/{id}/pdf', [AdminController::class, 'downloadInvoice'])->name('pdf');


    // Delete invoice
    Route::get('/view/{id}', [AdminController::class, 'showInvoice'])->name('view');
    Route::delete('/{id}', [AdminController::class, 'invoiceDelete'])->name('delete');

});



    
    // ===============================
    // STAFF MANAGEMENT
    // ===============================
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/list', [AdminController::class, 'staffList'])->name('list');
        Route::get('/department', [AdminController::class, 'departmentList'])->name('department');
        Route::get('/position', [AdminController::class, 'positionList'])->name('position');
        Route::get('/user', [AdminController::class, 'userManagement'])->name('user');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'userManagement'])->name('admin.users');
        Route::post('/users/store', [AdminController::class, 'userStore'])->name('admin.users.store');
        Route::put('/users/update/{id}', [AdminController::class, 'userUpdate'])->name('admin.users.update');
        Route::delete('/users/delete/{id}', [AdminController::class, 'userDelete'])->name('admin.users.delete');
    });
    
    // ===============================
// USER MANAGEMENT CRUD
// ===============================
Route::prefix('users')->name('users.')->group(function () {

    Route::get('/', [AdminController::class, 'userManagement'])->name('index');
    Route::post('/store', [AdminController::class, 'userStore'])->name('store');
    Route::put('/update/{id}', [AdminController::class, 'userUpdate'])->name('update');
    Route::delete('/destroy/{id}', [AdminController::class, 'userDestroy'])->name('destroy');

});

Route::prefix('departments')->name('departments.')->group(function () {

    Route::get('/', [AdminController::class, 'departmentList'])->name('index');
    Route::post('/store', [AdminController::class, 'departmentStore'])->name('store');
    Route::put('/update/{id}', [AdminController::class, 'departmentUpdate'])->name('update');
    Route::delete('/destroy/{id}', [AdminController::class, 'departmentDestroy'])->name('destroy');

});

 // =========================
    // Staff Routes (consistent with departments)
    // =========================
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [AdminController::class, 'staffList'])->name('index');
        Route::post('/store', [AdminController::class, 'staffStore'])->name('store');
        Route::put('/update/{id}', [AdminController::class, 'staffUpdate'])->name('update');
        Route::delete('/destroy/{id}', [AdminController::class, 'staffDestroy'])->name('destroy');
    });

 // =========================
    // Position Routes (same pattern)
    // =========================
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [AdminController::class, 'positionList'])->name('index');
        Route::post('/store', [AdminController::class, 'positionStore'])->name('store');
        Route::put('/update/{id}', [AdminController::class, 'positionUpdate'])->name('update');
        Route::delete('/destroy/{id}', [AdminController::class, 'positionDestroy'])->name('destroy');
    });



    // ===============================
    // GEOGRAPHICAL LOCATION
    // ===============================
    Route::prefix('geo')->name('geo.')->group(function () {
        Route::get('/regional', [AdminController::class, 'regionalList'])->name('regional');
        Route::get('/district', [AdminController::class, 'districtList'])->name('district');
    });

    

    Route::prefix('regionals')->name('regionals.')->group(function () {
        Route::get('/', [AdminController::class, 'regionalList'])->name('index');
        Route::post('/store', [AdminController::class, 'regionalStore'])->name('store');
        Route::put('/update/{id}', [AdminController::class, 'regionalUpdate'])->name('update');
        Route::delete('/destroy/{id}', [AdminController::class, 'regionalDestroy'])->name('destroy');
 });
    

 // Districts
        Route::prefix('district')->name('district.')->group(function () {
            Route::get('/', [AdminController::class, 'districtList'])->name('index');       // admin.geo.district.index
            Route::post('/store', [AdminController::class, 'districtStore'])->name('store'); // admin.geo.district.store
            Route::put('/update/{id}', [AdminController::class, 'districtUpdate'])->name('update'); // admin.geo.district.update
            Route::delete('/destroy/{id}', [AdminController::class, 'districtDestroy'])->name('destroy'); // admin.geo.district.destroy
        });
    // ===============================
    // BACKUP
    // ===============================
        Route::get('/backup', [AdminController::class, 'backupPage'])->name('backup');  // Main backup page: /admin/backup
        Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/run', [AdminController::class, 'runBackup'])->name('run');  // Run backup: /admin/backup/run
        Route::get('/download/{file}', [AdminController::class, 'downloadBackup'])->name('download'); // Download backup: /admin/backup/download/{file}
        Route::delete('/delete/{file}', [AdminController::class, 'deleteBackup'])->name('delete');  // Delete backup: /admin/backup/delete/{file}
    });


});



// ===============================
// CASHIER ROUTES
// ===============================
Route::prefix('cashier')->name('cashier.')->group(function () {

    // Dashboard
    Route::get('/', [CashierController::class, 'dashboard'])->name('index');

    // ===============================
    // CASHIER OPERATIONS
    // ===============================
    Route::get('/pos', [CashierController::class, 'pos'])->name('pos');
    Route::get('/deposit', [CashierController::class, 'deposit'])->name('deposit');
    Route::get('/incompletepayments', [CashierController::class, 'incompletepayments'])->name('incompletepayments');
    Route::get('/payments', [CashierController::class, 'payments'])->name('payments');

    // ===============================
    // EXPENDITURE MANAGEMENT
    // ===============================
    Route::get('/expenditure/list', [CashierController::class, 'expenditureList'])->name('expenditure');
    Route::get('/expenditure/category', [CashierController::class, 'expenditureCategory'])->name('expenditureCategory');

    // ===============================
    // REPORTS
    // ===============================
    Route::get('/report', [CashierController::class, 'report'])->name('report');

    // ===============================
    // CUSTOMERS & SUPPLIERS
    // ===============================
    Route::get('/customers', [CashierController::class, 'customers'])->name('customers');
    Route::get('/suppliers', [CashierController::class, 'suppliers'])->name('suppliers');

    // ===============================
    // EXTRA PAGES
    // ===============================
    Route::get('/quotations', [CashierController::class, 'quotations'])->name('quotations');
    Route::get('/invoices', [CashierController::class, 'invoices'])->name('invoices');


    // =====================
// DASHBOARD ROUTE
// =====================
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [CashierController::class, 'dashboard'])->name('index');
});

    // ======================
// CASHIER DEPOSITS
// ======================
    Route::get('/deposit', [CashierController::class, 'depositIndex'])->name('deposit.index');
    Route::post('/deposit/store', [CashierController::class, 'depositStore'])->name('deposit.store');

// ===============================
// ITEMS MANAGEMENT
// ===============================
Route::prefix('items')->name('items.')->group(function () {

    // ===============================
    // ITEMS MANAGEMENT PAGES
    // ===============================

    // Item Category
    Route::get('/category', [CashierController::class, 'itemCategory'])->name('category');

    // Item Sub Category
    Route::get('/subcategory', [CashierController::class, 'itemSubCategory'])->name('subcategory');

    // Item Name
    Route::get('/name', [CashierController::class, 'itemName'])->name('name');

    // Entry Type
    Route::get('/entry-type', [CashierController::class, 'entryType'])->name('entryType');

    // Inventory / Stock
    Route::get('/stock', [CashierController::class, 'inventory'])->name('stock');

    // Stock Adjustment
    Route::get('/adjustment', [CashierController::class, 'stockAdjustment'])->name('adjustment');

    // Items Out of Stock
    Route::get('/outofstock', [CashierController::class, 'outOfStock'])->name('outofstock');

    // Items Expired
    Route::get('/expired', [CashierController::class, 'expiredItems'])->name('expired');

// ===============================
    // CATEGORY CRUD
    // ===============================
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CashierController::class, 'categoryIndex'])->name('index');
        Route::get('/create', [CashierController::class, 'categoryCreate'])->name('create');
        Route::post('/store', [CashierController::class, 'categoryStore'])->name('store');
        Route::get('/edit/{id}', [CashierController::class, 'categoryEdit'])->name('edit');
        Route::put('/update/{id}', [CashierController::class, 'categoryUpdate'])->name('update');
        Route::delete('/delete/{id}', [CashierController::class, 'categoryDestroy'])->name('destroy');
    });



    // ===============================
    // SUBCATEGORY CRUD
    // ===============================
    Route::prefix('subcategories')->name('subcategories.')->group(function () {
        Route::get('/', [CashierController::class, 'itemSubCategory'])->name('index');
        Route::post('/store', [CashierController::class, 'subcategoryStore'])->name('store');
        Route::get('/edit/{id}', [CashierController::class, 'subcategoryEdit'])->name('edit');
        Route::put('/update/{id}', [CashierController::class, 'subcategoryUpdate'])->name('update');
        Route::delete('/delete/{id}', [CashierController::class, 'subcategoryDestroy'])->name('destroy');
    });


    // ===============================
    // ITEM NAME CRUD
    // ===============================
    Route::prefix('names')->name('names.')->group(function () {
        Route::get('/', [CashierController::class, 'itemName'])->name('index');
        Route::post('/store', [CashierController::class, 'itemNameStore'])->name('store');
        Route::get('/edit/{id}', [CashierController::class, 'itemNameEdit'])->name('edit');
        Route::put('/update/{id}', [CashierController::class, 'itemNameUpdate'])->name('update');
        Route::delete('/delete/{id}', [CashierController::class, 'itemNameDestroy'])->name('destroy');
    });


    // ===============================
    // ENTRY TYPE CRUD
    // ===============================
    Route::prefix('entrytype')->name('entrytype.')->group(function () {
        Route::get('/', [CashierController::class, 'entryType'])->name('index');
        Route::post('/store', [CashierController::class, 'entryTypeStore'])->name('store');
        Route::put('/update/{id}', [CashierController::class, 'entryTypeUpdate'])->name('update');
        Route::delete('/delete/{id}', [CashierController::class, 'entryTypeDestroy'])->name('destroy');
    });


    // ===============================
    // INVENTORY / STOCK CRUD
    // ===============================
    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Inventory main page
        Route::get('/', [CashierController::class, 'inventory'])->name('index');

        // Store stock
        Route::post('/store', [CashierController::class, 'inventoryStore'])->name('store');

        // Update stock transaction
        Route::put('/update/{id}', [CashierController::class, 'inventoryUpdate'])->name('update');

        // Delete stock transaction
        Route::delete('/delete/{id}', [CashierController::class, 'inventoryDestroy'])->name('destroy');


        // ===============================
        // STOCK ADJUSTMENT
        // ===============================
        Route::get('/adjustment', [CashierController::class, 'stockAdjustment'])->name('adjust');
        Route::post('/adjustment/store', [CashierController::class, 'stockAdjustmentStore'])->name('adjust.store');


        // ===============================
        // OUT OF STOCK
        // ===============================
        Route::get('/out-of-stock', [CashierController::class, 'outOfStock'])->name('out-of-stock');


        // ===============================
        // EXPIRED ITEMS
        // ===============================
        Route::get('/expired', [CashierController::class, 'expiredItems'])->name('expired');


        // ===============================
        // ITEM TRANSACTIONS / HISTORY
        // ===============================
        Route::get('/transactions/{item}', [CashierController::class, 'itemTransactions'])->name('transactions');
    });
});

// POS
    Route::get('/pos', [CashierController::class, 'pos'])->name('pos');
    Route::post('/pos/store', [CashierController::class, 'posStore'])->name('pos.store');

// PAYMENT LIST
    Route::get('/payments', [CashierController::class, 'paymentList'])->name('payments');

    
// INCOMPLETE PAYMENT
    Route::get('/incompletepayments',[CashierController::class, 'incompletePayments'])->name('incompletepayments');
     Route::post('/pay-sale/{sale}',[CashierController::class, 'paySale'])->name('pay.sale.store');
    
    
 // EXPENDITURE ROUTES - Fixed
 Route::prefix('expenditure')->name('expenditure.')->group(function () {
    // List
    Route::get('/list', [CashierController::class, 'expenditureIndex'])->name('list');
    Route::post('/store', [CashierController::class, 'storeExpenditure'])->name('store');
    Route::put('/update/{id}', [CashierController::class, 'updateExpenditure'])->name('update');
    Route::delete('/delete/{id}', [CashierController::class, 'destroyExpenditure'])->name('destroy');
    
    // Category
    Route::get('/category', [CashierController::class, 'expenditureCategoryIndex'])->name('category');
    Route::post('/category/store', [CashierController::class, 'storeExpenditureCategory'])->name('category.store');
    Route::put('/category/update/{id}', [CashierController::class, 'updateExpenditureCategory'])->name('category.update');
    Route::delete('/category/delete/{id}', [CashierController::class, 'destroyExpenditureCategory'])->name('category.destroy');
});
    
// CUSTOMERS
Route::prefix('customers')->name('customers.')->middleware('auth')->group(function () {
    Route::get('/', [CashierController::class, 'customers'])->name('index');
    Route::post('/store', [CashierController::class, 'storeCustomer'])->name('store');
    Route::delete('/delete/{id}', [CashierController::class, 'destroyCustomer'])->name('destroy');

});

Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [CashierController::class, 'quotations'])->name('index');
    Route::post('/store', [CashierController::class, 'storeQuotation'])->name('store');
    Route::delete('/delete/{id}', [CashierController::class, 'destroyQuotation'])->name('destroy');
});

Route::prefix('invoices')->name('invoices.')->middleware('auth')->group(function () {
    Route::get('/', [CashierController::class, 'invoices'])->name('index');
    Route::post('/store', [CashierController::class, 'storeInvoice'])->name('store');
    Route::delete('/delete/{id}', [CashierController::class, 'destroyInvoice'])->name('destroy');
    Route::get('/pdf/{id}', [CashierController::class, 'pdfInvoice'])->name('pdf'); // Add PDF route
    Route::get('/get/{id}', [CashierController::class, 'getInvoice'])->name('get'); // Add get invoice route
});
});

});

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');


<?php

use App\Http\Controllers\Contacts\ContactController;
use App\Http\Controllers\Gallery\GalleryCategoryController;
use App\Http\Controllers\Gallery\GalleryController;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Promotions\PromotionController;
use App\Http\Controllers\Receipts\CustomerController;
use App\Http\Controllers\Receipts\DeliveryNoteController;
use App\Http\Controllers\Receipts\InvoiceController;
use App\Http\Controllers\Receipts\NavigationLinkController;
use App\Http\Controllers\Receipts\PurchaseOrderController;
use App\Http\Controllers\Receipts\QuotationController;
use App\Http\Controllers\Receipts\ReceiptController;
use App\Http\Controllers\Receipts\SupplierController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\RolesAndPermissions\RolesAndPermissionController;
use App\Http\Controllers\Services\ServicesController;
use App\Http\Controllers\Transactions\TransactionsController;
use App\Http\Controllers\UserSearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');











        Route::get('/roles-permissions', fn () => Inertia::render('RolesPermissions'))->name('roles.permissions');

        Route::get('/roles', [RolePermissionController::class, 'roles']);
        Route::post('/roles', [RolePermissionController::class, 'storeRole']);

        Route::get('/permissions', [RolePermissionController::class, 'permissions']);
        Route::post('/permissions', [RolePermissionController::class, 'storePermission']);

        Route::get('/users', [RolePermissionController::class, 'users']);
        Route::post('/users/assign-role', [RolePermissionController::class, 'assignRole']);


        Route::put('/roles/{id}', [RolePermissionController::class, 'update']);
        Route::post('/roles/bulk-delete', [RolePermissionController::class, 'bulkDestroy']);
        Route::delete('/roles/{id}', [RolePermissionController::class, 'destroy']);











            
        //navigation links
        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
        Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
        Route::get('/news', [NewsController::class, 'index'])->name('news.index');
        Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');
        Route::get('/rolesAndPermission', [RolePermissionController::class, 'rolesAndPermission'])->name('admin.rolesAndPermission');
       // Route::get('/rolesAndPermission', [RolesAndPermissionController::class, 'rolesAndPermission'])->name('admin.rolesAndPermission');
        Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.quotations');

        // Add these routes to your existing web.php
        Route::get('/search-users', [UserSearchController::class, 'search'])->name('users.search');
        Route::get('/users/{user}', [UserSearchController::class, 'show'])->name('users.show');

        //contact routes
        Route::get('/getcontacts', [ContactController::class, 'getAllContacts']);
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);

        //Galleries
        Route::post('/galleries', [GalleryController::class, 'store']);
        Route::get('/galleries', [GalleryController::class, 'getAllGallery']);
        Route::delete('/galleries/{contact}', [GalleryController::class, 'destroy']);
        Route::post('/gallery-categories', [GalleryCategoryController::class, 'store']);
        Route::get('/gallery-category', [GalleryCategoryController::class, 'index']);
        Route::get('/fetch-gallery-category', [GalleryCategoryController::class, 'index']);

        //promotions
        Route::get('/getPromotions', [PromotionController::class, 'getPromotion']);
        Route::post('/promotions', [PromotionController::class, 'store']);

        //News
        Route::post('/news', [NewsController::class, 'store']);
        Route::get('/getNews', [NewsController::class, 'getNews']);
        Route::delete('/news/{newsId}', [NewsController::class, 'destroy']);
        Route::patch('/news/{news}/publish', [NewsController::class, 'publish']);
        Route::patch('/news/{news}/unpublish', [NewsController::class, 'unpublish']);

        //Services
        Route::post('/services', [ServicesController::class, 'store']);
        Route::get('/getServices', [ServicesController::class, 'getServices']);
        Route::delete('/services/{servicesId}', [ServicesController::class, 'destroy']);
        Route::patch('/services/{service}/status', [ServicesController::class, 'toggleStatus']);

        //Transactions
        Route::post('/transactions', [TransactionsController::class, 'store']);
        Route::get('/getTransactions', [TransactionsController::class, 'getTransactions']);
        Route::delete('/transactions/{transactionsId}', [TransactionsController::class, 'destroy']);
        Route::patch('/transactions/{transaction}', [TransactionsController::class, 'update']);
        Route::put('/updateTransactions/{transaction}', [TransactionsController::class, 'updateTransactions']);
        Route::get('/transactions/{transaction}/edit', [TransactionsController::class, 'edit']);
        Route::get('/transactions/export/{status}', [TransactionsController::class, 'exportTransactions']);
        Route::get('/transactions/{id}/pdf', [TransactionsController::class, 'downloadTransactionPDF']);
        Route::get('/getTransactionReports', [TransactionsController::class, 'getTransactionReports']);
        Route::get('/exportReport', [TransactionsController::class, 'exportReport']);

        // Customer routes
        Route::get('customers', [CustomerController::class, 'index']);
        Route::post('customers', [CustomerController::class, 'store']);
        Route::put('customers/{customer}', [CustomerController::class, 'update']);
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy']);

        // routes/web.php
        Route::prefix('receipts')->group(function () {
            
        Route::get('quotations', [QuotationController::class, 'index']);
        Route::post('quotations', [QuotationController::class, 'store']);
        Route::get('quotations/{quotation}', [QuotationController::class, 'show']);
        Route::put('quotations/{quotation}', [QuotationController::class, 'update']);
        Route::delete('quotations/{quotation}', [QuotationController::class, 'destroy']);
        Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf']);

        // Invoice routes
        Route::get('invoices', [InvoiceController::class, 'index']);
        Route::post('invoices', [InvoiceController::class, 'store']);
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy']);
        Route::get('invoices/{invoice}/invoicePdf', [InvoiceController::class, 'invoicePdf']);

        // Receipt routes
        Route::get('getReceipts', [ReceiptController::class, 'getReceipts']);
        Route::post('receipts', [ReceiptController::class, 'store']);
        Route::get('receipts/{receipt}', [ReceiptController::class, 'show']);
        Route::put('receipts/{receipt}', [ReceiptController::class, 'update']);
        Route::delete('receipts/{receipt}', [ReceiptController::class, 'destroy']);
        Route::get('receipts/{receipt}/pdf', [ReceiptController::class, 'pdf']);

        // Purchase Order routes
        Route::get('purchase-orders', [PurchaseOrderController::class, 'index']);
        Route::post('purchase-orders', [PurchaseOrderController::class, 'store']);
        Route::get('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'show']);
        Route::put('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'update']);
        Route::delete('purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'destroy']);
        Route::get('purchase-orders/{purchase_order}/pdf', [PurchaseOrderController::class, 'pdf']);

        // Supplier routes
        Route::get('getSuppliers', [SupplierController::class, 'index']);
        Route::post('suppliers', [SupplierController::class, 'store']);
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show']);
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update']);
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy']);

        // Delivery Note routes
        Route::get('delivery-notes', [DeliveryNoteController::class, 'index']);
        Route::post('delivery-notes', [DeliveryNoteController::class, 'store']);
        Route::get('delivery-notes/{delivery_note}', [DeliveryNoteController::class, 'show']);
        Route::put('delivery-notes/{delivery_note}', [DeliveryNoteController::class, 'update']);
        Route::delete('delivery-notes/{delivery_note}', [DeliveryNoteController::class, 'destroy']);
        Route::get('delivery-notes/{delivery_note}/pdf', [DeliveryNoteController::class, 'pdf']);

        });

    });

    
    //Navigation links interface
    Route::post('/contact', [ContactController::class, 'store']);
    Route::get('/displayGalleries', [GalleryController::class, 'displayGalleries']);
    Route::get('/getLatestNews', [NewsController::class, 'getLatestNews']);
    Route::get('/getPromotions', [PromotionController::class, 'getPromotions']);
    Route::get('/getAllServices', [ServicesController::class, 'getAllServices']);
    
    //render page
    Route::get('about-us', function () {
        return Inertia::render('Site/aboutUs');
    })->name('aboutUs');

    Route::get('/services-list', function () {
        return Inertia::render('MyMARZ/Services/ServicesList'); // new Vue component name
    })->name('servicesList');
  
    Route::get('contact-us', function () {
        return Inertia::render('Site/Contact');
    })->name('contactDetails');

    Route::get('/promotions/{id}', [PromotionController::class, 'showPromotion'])
    ->name('promotions.showPromotion');

    Route::get('/news/{id}', [NewsController::class, 'showNews'])
    ->name('news.showNews');

   
















require __DIR__.'/auth.php';

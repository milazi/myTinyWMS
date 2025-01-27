<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'DashboardController@index');

Route::group(['middleware' => ['auth']], function () {
    Route::namespace('Article')->middleware(['permission:article.view'])->group(function () {
        Route::group(['middleware' => ['permission:article.edit']], function () {
            Route::get('article/sort-update', 'SortController@index')->name('article.sort_update_form');
            Route::post('article/sort-update', 'SortController@store')->name('article.sort_update_form_post');

            Route::get('article/mass-update', 'MassUpdateController@index')->name('article.mass_update_form');
            Route::post('article/mass-update', 'MassUpdateController@store')->name('article.mass_update_save');

            Route::get('article/inventory-update', 'InventoryUpdateController@index')->name('article.inventory_update_form');
            Route::post('article/inventory-update', 'InventoryUpdateController@store')->name('article.inventory_update_save');

            Route::post('article/{article}/change-changelog-note', 'QuantityChangelogController@changeChangelogNote')->name('article.change_changelog_note');
            Route::get('article/{article}/quantity-changelog/{changelog}/delete', 'QuantityChangelogController@delete')->name('article.quantity_changelog.delete');

            Route::post('article/{article}/change-supplier', 'SupplierController@store')->name('article.change_supplier');
        });

        Route::group(['middleware' => ['permission:article.change_quantity']], function () {
            Route::post('article/{article}/change-quantity', 'ArticleController@changeQuantity')->name('article.change_quantity');
            Route::post('article/{article}/fix-quantity-change', 'ArticleController@fixQuantityChange')->name('article.fix_quantity_change');
        });

        Route::get('article/{article}/file_delete/{file}', 'AttachmentController@delete')->middleware(['permission:article.delete.file'])->name('article.file_delete');
        Route::post('article/{article}/file_upload', 'AttachmentController@upload')->middleware(['permission:article.create.file'])->name('article.file_upload');

        Route::get('article/{article}/file-download/{file}', 'AttachmentController@download')->name('article.file_download');

        Route::get('article/{article}/print-label/{size}', 'LabelController@printSingleLabel')->name('article.print_single_label');
        Route::post('article/print-label', 'LabelController@printLabel')->name('article.print_label');

        Route::post('article/{article}/addnote', 'NoteController@store')->middleware(['permission:article.create.note'])->name('article.add_note');
        Route::get('article/{article}/deletenote/{note}', 'NoteController@delete')->middleware(['permission:article.delete.note'])->name('article.delete_note');

        Route::get('article/{article}/quantity-changelog', 'QuantityChangelogController@index')->name('article.quantity_changelog');

        Route::get('article/{article}/copy', 'ArticleController@copy')->middleware(['permission:article.create'])->name('article.copy');
        Route::get('article/{article}/delete', 'ArticleController@delete')->middleware(['permission:article.edit'])->name('article.delete');
    });

    Route::namespace('Admin')->prefix('admin')->middleware(['permission:admin'])->group(function () {
        Route::get('/', 'StartController@index')->name('admin.index');

        Route::get('/settings', 'SettingsController@show')->name('admin.settings.show');
        Route::post('/settings', 'SettingsController@save')->name('admin.settings.save');

        Route::resources([
            'category' => 'CategoryController',
            'unit' => 'UnitController',
            'user' => 'UserController',
            'role' => 'RoleController',
        ]);
    });

    Route::group(['middleware' => ['permission:inventory.edit']], function () {
        Route::post('inventory/{inventory}/article/{article}/processed', 'InventoryController@processed')->name('inventory.processed');
        Route::get('inventory/{inventory}/article/{article}/correct', 'InventoryController@correct')->name('inventory.correct');
        Route::get('inventory/{inventory}/category/{category}/done', 'InventoryController@categoryDone')->name('inventory.category.done');
        Route::get('inventory/{inventory}/finish', 'InventoryController@finish')->name('inventory.finish');
    });

    Route::group(['middleware' => ['permission:inventory.create']], function () {
        Route::get('inventory/create_month', 'InventoryController@createMonth')->name('inventory.create_month');
        Route::get('inventory/create_year', 'InventoryController@createYear')->name('inventory.create_year');
    });

    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::resources([
        'article' => 'Article\ArticleController',
        'article-group' => 'Article\ArticleGroupController',
        'supplier' => 'SupplierController',
        'order' => 'Order\OrderController',
        'inventory' => 'InventoryController',
    ]);

    Route::group(['middleware' => ['permission:article-group.change_quantity']], function () {
        Route::post('article-group/{article_group}/change-quantity', 'Article\ArticleGroupController@changeQuantity')->name('article-group.change_quantity');
    });

    Route::group(['middleware' => ['permission:reports.view']], function () {
        Route::get('reports', 'ReportsController@index')->name('reports.index');
        Route::get('reports/deliveries-without-invoice', 'ReportsController@deliveriesWithoutInvoice')->name('reports.deliveries_without_invoice');
        Route::get('reports/invoices-without-delivery', 'ReportsController@invoicesWithoutDelivery')->name('reports.invoices_without_delivery');
        Route::get('reports/inventory-pdf', 'ReportsController@generateInventoryPdf')->name('reports.inventory_pdf');
        Route::get('reports/yearly-inventory-pdf', 'ReportsController@generateYearlyInventoryPdf')->name('reports.yearly_inventory_pdf');
        Route::post('reports/inventory-report', 'ReportsController@generateInventoryReport')->name('reports.inventory_report');
        Route::post('reports/article-usage-report', 'ReportsController@generateArticleUsageReport')->name('reports.article_usage_report');
        Route::post('reports/article-weight-report', 'ReportsController@generateArticleWeightReport')->name('reports.article_weight_report');
        Route::post('reports/deliveries-with-invoice', 'ReportsController@deliveriesWithInvoice')->name('reports.invoices_with_delivery');
        Route::get('reports/deliveries-with-invoice/export', 'ReportsController@deliveriesWithInvoiceExport')->name('reports.invoices_with_delivery_export');
        Route::post('reports/print-category-list', 'ReportsController@printCategoryList')->name('reports.print_category_list');
    });

    Route::get('notification/{id}/delete', 'NotificationController@delete');

    Route::get('settings', 'SettingsController@show')->name('settings.show');
    Route::post('settings', 'SettingsController@save')->name('settings.save');
    Route::get('change_pw', 'SettingsController@changePwForm')->name('settings.change_pw');
    Route::post('change_pw', 'SettingsController@changePw')->name('settings.change_pw_post');
    Route::post('create_token', 'SettingsController@createToken')->name('settings.create_token');
    Route::get('remove_token/{token}', 'SettingsController@removeToken')->name('settings.remove_token');

    Route::post('global-search', 'GlobalSearchController@process')->name('global_search');

    Route::namespace('Order')->middleware(['permission:order.view'])->group(function () {
        Route::group(['middleware' => ['permission:order.edit']], function () {
            Route::post('order/{orderitem}/item-invoice-received', 'OrderItemsController@invoiceReceived')->name('order.item_invoice_received');
            Route::get('order/{orderitem}/item-confirmation-status/{status}', 'OrderItemsController@confirmationReceived')->name('order.item_confirmation_received');
            Route::post('order/{order}/all-items-invoice-received', 'OrderItemsController@allItemsInvoiceReceived')->name('order.all_items_invoice_received');
            Route::post('order/{order}/all-items-confirmation-received', 'OrderItemsController@allItemsConfirmationReceived')->name('order.all_items_confirmation_received');

            Route::get('order/{order}/payment-status/{payment_status}', 'OrderController@changePaymentStatus')->name('order.change_payment_status');
            Route::get('order/{order}/status/{status}', 'OrderController@changeStatus')->name('order.change_status');
            Route::post('order/{order}/invoicecheck/upload', 'OrderController@uploadInvoiceCheckAttachments')->name('order.invoice_check_upload');
            Route::post('order/{order}/set-invoice-number', 'OrderController@setInvoiceNumber')->name('order.set_invoice_number');
        });

        Route::group(['middleware' => ['permission:order.create']], function () {
            Route::post('order/create', 'OrderController@create')->name('order.create_post');
        });

        Route::group(['middleware' => ['permission:order.edit']], function () {
            Route::get('order/{order}/cancel', 'OrderController@cancel')->name('order.cancel');
            Route::get('order/{order}/create-delivery', 'DeliveryController@create')->name('order.create_delivery');
            Route::post('order/{order}/store-delivery', 'DeliveryController@store')->name('order.store_delivery');
            Route::delete('order/{order}/delivery/{delivery}', 'DeliveryController@delete')->name('order.delete_delivery');
        });

        Route::group(['middleware' => ['permission:ordermessage.create']], function () {
            Route::get('order/{order}/message/new', 'MessageController@create')->name('order.message_new');
            Route::post('order/{order}/message/new', 'MessageController@store')->name('order.message_create');
            Route::post('order/{order}/message/upload', 'MessageAttachmentController@upload')->name('order.message_upload');
        });

        Route::group(['middleware' => ['permission:ordermessage.edit']], function () {
            Route::get('order/message/{message}/delete/{order?}', 'MessageController@delete')->name('order.message_delete');
            Route::get('order/{order}/message/{message}/read', 'MessageController@markRead')->name('order.message_read');
            Route::get('order/{order}/message/{message}/unread', 'MessageController@markUnread')->name('order.message_unread');

            Route::post('order/message/assign', 'UnassignedMessagesController@assignToOrder')->name('order.message_assign');
            Route::get('order/message/{message}/forward', 'MessageForwardController@create')->name('order.message_forward_form');
            Route::post('order/message/{message}/forward', 'MessageForwardController@store')->name('order.message_forward');
        });

        Route::get('order/message/{message}/attachment-download/{attachment}', 'MessageAttachmentController@download')->name('order.message_attachment_download');

        Route::get('order/message/unassigned', 'UnassignedMessagesController@index')->middleware(['permission:ordermessage.view'])->name('order.messages_unassigned');
    });
});

<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Client')->as('client.')->middleware(['auth', 'subscription_track'])->group(function () {
    // DASHBOARD
    Route::get('/dashboard', 'Dashboard\DashboardController@index')->name('dashboard');
    // PROFILE
    Route::get('profiles', 'Profile\ProfilesController@edit')->name('profiles.edit');
    Route::post('profiles', 'Profile\ProfilesController@update')->name('profiles.update');
    // ROLE
    Route::resource('/roles', 'Administration\RolesController');
    // USER
    Route::resource('/users', 'Administration\UsersController');
    // BILLING ADDRESS
    Route::get('billing-address', 'Billing\BillingAddressController@edit')->name('billings.address.edit');
    Route::post('billing-address', 'Billing\BillingAddressController@update')->name('billings.address.update');
    // SUBSCRIPTION
    Route::get('/subscription', 'Subscription\SubscriptionsController@packageList')->name('subscription.packageList');
    Route::get('/subscription/checkout/{id}', 'Subscription\SubscriptionsController@checkout')->name('subscription.checkout');
    Route::post('/subscription/checkout/{id}', 'Subscription\SubscriptionsController@pay')->name('subscription.pay');
    Route::get('/subscription/history', 'Subscription\SubscriptionsController@history')->name('subscription.history');
    Route::get('/subscription/invoice/{id}', 'Subscription\SubscriptionsController@invoice')->name('subscription.invoice');
    Route::get('/subscription/invoice-download/{id}', 'Subscription\SubscriptionsController@invoiceDownload')->name('subscription.invoiceDownload');
    // SUPPLIER
    Route::resource('/suppliers', 'Supplier\SuppliersController');
    // WAREHOUSE
    Route::resource('/warehouses', 'Warehouse\WarehousesController');
    // PRODUCT CATEGORY
    Route::resource('/product-categories', 'Product\ProductCategoriesController');
    // ASSIGN SKU TO CATEGORY
    Route::get('/category/assing-sku', 'Product\ProductCategoriesController@assignSku')->name('category.assingSku');
    // AMAZON PAY
    Route::get('/amazon-pay/checkout-review', 'Payment\AmazonPayController@checkoutReview');
    Route::get('/amazon-pay/checkout-result', 'Payment\AmazonPayController@checkoutResult');


    // ECOM INTEGRATION
    Route::prefix('ecom-platforms')->group(function () {
        // Ecom integration list
        Route::get('/', 'EcomPlatform\EcomPlatformsController@index')->name('ecom-platforms.index');
        // Display connect page
        Route::get('/connect', 'EcomPlatform\EcomPlatformsController@connect')->name('ecom-platforms.connect');
        // Store ebay connection info
        Route::get('/consent', 'EcomPlatform\EcomPlatformsController@connectStore')->name('ecom-platforms.ebay.connectStore');
        // Store connection info
        Route::post('/connect', 'EcomPlatform\EcomPlatformsController@connectStore')->name('ecom-platforms.connectStore');
        // Disconnect platform
        Route::delete('/disconnect/{platform}', 'EcomPlatform\EcomPlatformsController@disconnect')->name('ecom-platforms.disconnect');
    });

    // PRODUCT
    Route::get('products', 'Product\ProductsController@index')->name('products.index');
    Route::get('products/create', 'Product\ProductsController@create')->name('products.create');
    Route::post('products/store', 'Product\ProductsController@store')->name('products.store');
    Route::post('products', 'Product\ProductsController@index');
    Route::post('products/sync/{platform}', 'Product\ProductsController@sync')->name('products.sync');
    Route::get('products/edit-basic-info/{id}', 'Product\ProductsController@editBasicInfo')->name('products.edit.basicInfo');
    Route::get('products/edit-desc/{id}', 'Product\ProductsController@editDesc')->name('products.edit.desc');
    Route::get('products/edit-images/{id}', 'Product\ProductsController@editImages')->name('products.edit.images');
    Route::get('products/edit-inventory/{id}', 'Product\ProductsController@editInventory')->name('products.edit.inventory');
    Route::get('products/edit-tags/{id}', 'Product\ProductsController@editTags')->name('products.edit.tags');
    Route::get('products/delete-image/{product_id}/{image_id}', 'Product\ProductsController@deleteProductImage')->name('products.delete.image');

    Route::post('products/edit-basic-info/{id}', 'Product\ProductsController@updateBasicInfo')->name('products.update.basicInfo');
    Route::post('products/edit-desc/{id}', 'Product\ProductsController@updateDesc')->name('products.update.desc');
    Route::post('products/edit-images/{id}', 'Product\ProductsController@updateImages')->name('products.update.images');
    Route::post('products/edit-inventory/{id}', 'Product\ProductsController@updateInventory')->name('products.update.inventory');
    Route::post('products/edit-tags/{id}', 'Product\ProductsController@updateTags')->name('products.update.tags');

    Route::delete('products/{id}/destroy', 'Product\ProductsController@destroy')->name('products.destroy');

    Route::post('products-export', 'Product\ProductsController@exportProduct')->name('products.export');

    Route::get('system-products', 'Product\ProductsController@systemProducts')->name('products.system.products');
    Route::post('system-products-export', 'Product\ProductsController@exportSystemProduct')->name('products.system.export');
    Route::get('products-stock-level', 'Product\ProductsController@stockLevel')->name('products.stock.level');
    Route::post('stock-level-products-export', 'Product\ProductsController@exportStockLevel')->name('products.stockLevel.export');

    // Ebay category
    Route::get('product/ebay-categories/{id}', 'Product\ProductsController@getChildrenForTree')->name('products.ebay-categories.show');
    Route::post('product/ebay-categories/condition', 'Product\ProductsController@getCategoryCondition')->name('products.ebay-categories.condition');
    // Etsy Taxonomy
    Route::get('product/etsy-taxonomies/{id}', 'Product\ProductsController@getChildrenForEtsyTree')->name('products.etsy-taxonomies.show');
    // Product condition
    Route::resource('product-conditions', 'Product\ProductConditionsController');

    // ORDER
    Route::resource('orders', 'Order\OrdersController');
    Route::post('orders/sync/{platform}', 'Order\OrdersController@sync')->name('orders.sync');
    Route::get('orders/invoice/download/{id}', 'Order\OrdersController@invoiceDownload')->name('orders.invoice.download');

    // SHIPPING ORDER  
    Route::get('orders/shipped/details/{order_id}', 'Order\OrdersController@shippedOrderDetails')->name('orders.shipped.details');
    Route::get('orders/shipped/all', 'Order\OrdersController@shippedOrders')->name('orders.shipped');
    Route::get('orders/pending-to-shipped/index', 'Order\OrdersController@pendingToShipped')->name('orders.pending_to_shipped');
    Route::get('orders/pending-to-shipped/list', 'Order\OrdersController@pendingToShippedList')->name('orders.pending_to_shipped_list');

    // CATEGORY SKU
    Route::get('categories-skus/{category_id}', 'CategoriesSkus\CategoriesSkusController@categorySkus')->name('categoreis.skus.list');
    Route::delete('categories-skus/detach', 'CategoriesSkus\CategoriesSkusController@detach')->name('categoreis.skus.detach');
    Route::post('categories-skus/assign/{category_id}', 'CategoriesSkus\CategoriesSkusController@assign')->name('categoreis.skus.assign');

    // CUSTOM SHIPPING 
    Route::resource('/shippings', 'Shipping\ShippingController')->except(['create']);
    Route::get('/shippings/get-rates/{shipping_id}', 'Shipping\ShippingController@getRates')->name('shippings.get_rates');
    Route::get('/shippings/process-shipping/{shipping_id}/{courier_id}/{courier_name}', 'Shipping\ShippingController@processShipping')->name('shippings.process_shipping');
    Route::get('/shippings/create/{order_id}', 'Shipping\ShippingController@create')->name('shippings.create');



    // CUSTOM SKU
    Route::resource('custom-skus', 'CustomSku\CustomSkusController');

    // MERGE MULTIPLE SKU
    Route::get('custom-sku/merge-sku/{custom_sku_id}', 'MergeSku\MergeSkusController@mergeSkuList')->name('customSku.mergedSku');
    Route::delete('custom-sku/merge-sku/detach', 'MergeSku\MergeSkusController@detach')->name('customSku.mergedSku.detach');
    Route::get('merge-sku', 'MergeSku\MergeSkusController@mergeSku')->name('mergeSku');
    Route::post('merge-sku/assing/{custom_sku_id}', 'MergeSku\MergeSkusController@assign')->name('mergeSku.assign');

    // EXPENSES CATEGORY
    Route::resource('expenses-categories', 'Expenses\ExpensesCategoriesController');

    // EXPENSES
    Route::resource('expenses', 'Expenses\ExpensesController');
    Route::get('expenses/file/delete/{file_id}', 'Expenses\ExpensesController@deleteFile')->name('expenses.deleteFile');
    Route::get('expenses-report', 'Expenses\ExpensesController@generateReport')->name('expenses.report');
    Route::get('expenses-report-export', 'Expenses\ExpensesController@exportReport')->name('expenses.report.export');

    // ISSUES
    Route::resource('issues', 'Issue\IssuesController');
    Route::get('system-contacts', 'Contact\ContactsController@index')->name('system-contacts.index');

    // FAQ
    Route::get('faqs', 'Faq\FaqsController@index')->name('faqs.index');

    // BULK SKU UPLOAD
    Route::get('bulk-sku-upload', 'BulkSku\BulkSkuUploadsController@upload')->name('bulk_skus.upload');
    Route::post('bulk-sku-upload', 'BulkSku\BulkSkuUploadsController@uploadSubmit')->name('bulk_skus.upload.submit');

    // ARRIVING ITEMS
    Route::resource('arriving-items', 'ArrivingItem\ArrivingItemsController')->except(['update']);
    Route::post('arriving-items/update/{id}', 'ArrivingItem\ArrivingItemsController@update');
    Route::get('delivered-items', 'ArrivingItem\ArrivingItemsController@deliveredItem')->name('deleivered_items');

    // INVOICE
    Route::resource('invoices', 'Invoice\InvoicesController');
    Route::get('invoices/download/{id}', 'Invoice\InvoicesController@download')->name('invoices.download');
    Route::post('invoices/payments', 'Invoice\InvoicesController@addPayment')->name('invoices.add_payment');
    Route::get('invoices/payments/{invoice_id}', 'Invoice\InvoicesController@getPayments')->name('invoices.get_payments');
    Route::post('invoices/payments/send', 'Invoice\InvoicesController@sendInvoice')->name('invoices.sendInvoice');
    Route::get('invoices/payments/delete/{id}', 'Invoice\InvoicesController@deletePayment')->name('invoices.delete_payment');


    // SHIPPIG PROVIDER
    Route::prefix('shipping-providers')->group(function () {
        // Shipping provider integration list
        Route::get('/', 'ShippingProvider\ShippingProvidersController@index')->name('shipping-providers.index');
        // Display connect page
        Route::get('/connect', 'ShippingProvider\ShippingProvidersController@connect')->name('shipping-providers.connect');
        // Store connection info
        Route::post('/connect', 'ShippingProvider\ShippingProvidersController@connectStore')->name('shipping-providers.connectStore');
        // Edit connection info
        Route::get('/edit/{provider}', 'ShippingProvider\ShippingProvidersController@edit')->name('shipping-providers.edit');
        Route::put('/edit', 'ShippingProvider\ShippingProvidersController@update')->name('shipping-providers.update');
        // Disconnect shipping provider
        Route::delete('/disconnect/{provider}', 'ShippingProvider\ShippingProvidersController@disconnect')->name('shipping-providers.disconnect');
    });

    // Settings
    Route::get('setting/site-settings', 'Settings\SiteSettingController@index')->name('site-setting.index');
    Route::post('setting/site-settings', 'Settings\SiteSettingController@update')->name('site-setting.update');
    Route::get('setting/sync-policies', 'Settings\SiteSettingController@sync_policies')->name('setting.sync_policies');
    Route::get('setting/sync-shopify-locations', 'Settings\SiteSettingController@syncShopifyLocations')->name('setting.sync_shopify_location');
    Route::get('setting/sync-etsy-taxonomies', 'Settings\SiteSettingController@syncEtsyTaxonomy')->name('setting.sync_etsy_taxonomy');
    Route::get('setting/sync-etsy-shops', 'Settings\SiteSettingController@syncEtsyShops')->name('setting.sync_etsy_shops');



    // HANDLE API REQUEST
    Route::prefix('app/api')->as('app-api.')->group(function () {
        /**
         * Because this api only handle forntend API and ajax
         * request so added "app" at prefix. its done for future.
         * If user try to build full feature API they can use "/api" endpint
         * Thanks me later
         */


        // Get product sku by platform
        Route::get('/products/skus/{platform_id}', 'Product\ProductsController@productSkuByPlatform');
        // Search product by sku
        Route::get('/products/skus/search/{query}', 'Product\ProductsController@productSkuSearch');
        // Search product by name and sku
        Route::get('/products/name-sku/search/{query}', 'Product\ProductsController@productSearchByNameSku');
        // Get expenses
        Route::get('/expenses', 'Expenses\ExpensesController@apiGet')->name('expenses.get');
    });
});
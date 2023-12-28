<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Admin')->prefix('admin')->as('admin.')->middleware(['auth', 'system_admin'])->group(function () {
    // DASHBOARD
    Route::get('/dashboard', 'Dashboard\DashboardController@index')->name('dashboard');
    // APP CLIENTS
    Route::resource('clients', 'User\ClientsController');
    // SUBSCRIPTION PACKAGE
    Route::resource('subs-packages', 'Subscription\PackagesController')->except(['show']);
    // SUBSCRIPTION
    Route::get('subscription-invoices', 'Subscription\SubscriptionsController@invoices')->name('subscriptions.invoices');
    Route::get('subscription-invoices/{id}', 'Subscription\SubscriptionsController@invoiceDetails')->name('subscriptions.invoices.details');
    Route::get('subscription-invoices-download/{id}', 'Subscription\SubscriptionsController@invoiceDownload')->name('subscriptions.invoices.download');
    Route::get('subscription-invoices-send/{id}', 'Subscription\SubscriptionsController@invoiceSend')->name('subscriptions.invoices.send');
    // ISSUES
    Route::resource('issues', 'Issue\IssuesController');
    Route::resource('contacts', 'Contact\SystemContactsController');
    // FAQ
    Route::resource('faq-categories', 'Faq\FaqCategoriesController');
    Route::resource('faqs', 'Faq\FaqsController');
    Route::delete('faqs/image/delete/{id}', 'Faq\FaqsController@deleteImage')->name('faqs.delete-image');
    // SYSTEM SETTINGS
    Route::get('system-settings/general', 'Settings\SystemSettingsController@editGeneral')->name('system-settings.edit.general');
    Route::get('system-settings/api', 'Settings\SystemSettingsController@editApi')->name('system-settings.edit.api');
    Route::post('system-settings', 'Settings\SystemSettingsController@update')->name('system-settings.update');
    // NOTIFICATIONS
    Route::get('notifications', 'Notification\NotificationsController@index')->name('notifications.index');

    // HANDLE API REQUEST
    Route::prefix('app/api')->as('app-api.')->group(function () {
        /**
         * Because this api only handle forntend API and ajax
         * request so added "app" at prefix. its done for future.
         * If user try to build full feature API they can use "/api" endpint
         * Thanks me later
         */


        // Get unread notifications
        Route::get('/notifications/unread', 'Notification\NotificationsController@getUnreadJson');
        Route::get('/notifications/unread/count', 'Notification\NotificationsController@getUnreadCountJson');
    });
});

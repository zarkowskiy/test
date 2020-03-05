<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    /**
     * Webhook settings routes
     */
    $router->get('/webhook', 'WebhookController@index')->name('admin.webhook.index');
    $router->post('/webhook/setwebhook', 'WebhookController@setWebhook')->name('admin.webhook.setwebhook');

});

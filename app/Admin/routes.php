<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('cities', CitiesController::class);
    $router->get('/cities/{city}/places', 'CitiesController@places_grid')->name('admin.cities.places.index');
    /**
     * Settings routes
     */
    $router->get('/settings', 'SettingsController@index')->name('admin.settings.index');
    $router->post('/settings/setwebhook', 'SettingsController@setWebhook')->name('admin.settings.setwebhook');
    $router->post('/settings/setwelcome', 'SettingsController@setWelcome')->name('admin.settings.setwelcome');

});

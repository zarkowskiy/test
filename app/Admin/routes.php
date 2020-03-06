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
    $router->get('/cities/{city}/places', 'CitiesController@places_index')->name('admin.cities.places.index');
    $router->get('/cities/{city}/places/create', 'CitiesController@places_create')->name('admin.cities.places.create');
    $router->get('/cities/{city}/places/{place}/edit', 'CitiesController@places_edit')->name('admin.cities.places.edit');
    $router->post('/cities/{city}/places', 'CitiesController@places_save')->name('admin.cities.places.save');
    $router->put('/cities/{city}/places/{place}', 'CitiesController@places_save')->name('admin.cities.places.save');
    $router->get('/cities/{city}/places/{place}/schedule', 'CitiesController@schedule_edit')->name('admin.cities.places.schedule.edit');
    $router->match(['post','put'],'/cities/{city}/places/{place}/schedule/save', 'CitiesController@schedule_save')->name('admin.cities.places.schedule.save');
    /**
     * Settings routes
     */
    $router->get('/settings', 'SettingsController@index')->name('admin.settings.index');
    $router->post('/settings/setwebhook', 'SettingsController@setWebhook')->name('admin.settings.setwebhook');
    $router->post('/settings/setwelcome', 'SettingsController@setWelcome')->name('admin.settings.setwelcome');

});

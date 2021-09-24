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


//Uniform
Route::group([
    'middleware' => ['web', 'auth', 'permission:view_uniform'], //TODO:change later
    'prefix' => 'uniform',
    'namespace' => 'Modules\Uniform\Http\Controllers'
], function () {
    Route::group(['middleware' => ['permission:view_ura_transactions']], function () {
        //Ura transaction
        Route::get('ura-transactions', ['as' => 'ura.transactions', 'uses' => 'UraTransactionController@index']);
        Route::get('ura-transactions/list', ['as' => 'ura.transactions.list', 'uses' => 'UraTransactionController@list']);
        Route::post('ura-transactions/new', ['as' => 'ura.transactions.new', 'uses' => 'UraTransactionController@store']);
        Route::get('ura-balance-info', ['as' => 'ura.balance.info', 'uses' => 'UraTransactionController@getBalanceInfo']);
    });

    Route::group(['middleware' => ['permission:view_uniform_orders']], function () {
        //Uniform orders
        Route::get('uniform-orders', ['as' => 'uniform.orders', 'uses' => 'UniformOrderController@index']);
        Route::get('uniform-orders/list', ['as' => 'uniform.orders.list', 'uses' => 'UniformOrderController@list']);
        Route::get('uniform-orders/single/{id}', array('as' => 'uniform.orders.single', 'uses' => 'UniformOrderController@getSingle'));
        Route::get('uniform-orders/email-script/{id}', array('as' => 'uniform.email-script.single', 'uses' => 'UniformOrderController@getEmailTemplate'));
        Route::get('uniform-orders/{id}/items', array('as' => 'uniform.orders.items', 'uses' => 'UniformOrderController@getOrderItems'));
        Route::post('uniform-orders/update-status', ['as' => 'uniform.orders.update-status', 'uses' => 'UniformOrderController@updateStatus']);
    });

});

//Admin
Route::group([
    'middleware' => ['web', 'auth', 'permission:view_admin'],
    'prefix' => 'admin',
    'namespace' => 'Modules\Uniform\Http\Controllers\Admin'
], function () {
    //Ura rates
    Route::get('ura-rates', ['as' => 'ura.rates', 'uses' => 'UraRateController@index']);
    Route::get('ura-rates-list', ['as' => 'ura.rates.list', 'uses' => 'UraRateController@getList']);
    Route::post('ura-rates-store', ['as' => 'ura.rates.store', 'uses' => 'UraRateController@store']);

    //Uniform products
    Route::name('uniform-products')->get('uniform-products', 'UniformProductController@index');
    Route::get('uniform-products/list', array('as' => 'uniform-products.list', 'uses' => 'UniformProductController@getList'));
    Route::get('uniform-products/add', array('as' => 'uniform-products.add', 'uses' => 'UniformProductController@addUniformProductVariants'));
    Route::post('uniform-products/store', array('as' => 'uniform-products.store', 'uses' => 'UniformProductController@store'));
    Route::get('uniform-products/update/{id}', array('as' => 'uniform-products.update', 'uses' => 'UniformProductController@addUniformProductVariants'));
    Route::get('uniform-products/filedownload', array('as' => 'uniform-products.filedownload', 'uses' => 'UniformProductController@getVideoUrl'));
    Route::get('uniform-products/destroy/{id}', array('as' => 'uniform-products.destroy', 'uses' => 'UniformProductController@destroy'));
    Route::get("uniform-products/destroy-attachment/{id}", array('as' => "uniform-products.destroy-attachment", "uses" => "UniformProductController@destroyAttachment"));

    Route::get('ura-settings', ['as' => 'ura.settings', 'uses' => 'UraSettingsController@index']);
    Route::post('ura-settings', ['as' => 'ura.settings.store', 'uses' => 'UraSettingsController@store']);

});

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


Route::group(['middleware' => ['web','auth','permission:view_documents'], 'prefix' => 'documents',], function()
{
    // Route::get('/', 'DocumentsController@index');
    Route::group(['middleware' => ['permission:view_employee_document|add_employee_document|add_allocated_employee_document|view_allocated_employee_document']], function () {
        Route::get('/employee-document', array('as' => 'documents.employee-document', 'uses' => 'DocumentsController@index'));
        Route::get('employee-document/list/{checked?}', array('as' => 'employee-document.list', 'uses' => 'DocumentsController@getUserSummaryList'));
    });
    Route::group(['middleware' => ['permission:add_client_document|view_client_document|add_allocated_client_document|view_allocated_client_document']], function () {
        Route::get('/client-document', array('as' => 'documents.client-document', 'uses' => 'DocumentsController@clientDocument'));
        Route::get('client-document/list/{checked?}', array('as' => 'client-document.list', 'uses' => 'DocumentsController@getList'));
    });

    Route::get('view-document/{typeid?}/{id?}', array('as' => 'documents.view-document', 'uses' => 'DocumentsController@viewDocument'));
    Route::get('view-document/list/{typeid?}/{id?}/{checked?}', array('as' => 'view-list.document', 'uses' => 'DocumentsController@viewDocumentlist'));
    Route::get('add-document/{typeid?}/{id?}', array('as' => 'add-client.document', 'uses' => 'DocumentsController@addClientDocument'));
    Route::post('documents/{module}/store', array('as' => 'documents.store', 'uses' => 'DocumentsController@store'));
    Route::get('employee-document/documentnames/{id?}', array('as' => 'document-name-details.single', 'uses' => 'DocumentsController@getNameList'));
    Route::get('/other-vendor/{id?}', array('as' => 'documents.other-vendor', 'uses' => 'DocumentsController@otherVendor'));
    
    Route::get('other-vendor/list/{typeid?}/{id?}', array('as' => 'other-vendor.list', 'uses' => 'DocumentsController@otherVendorlist'));
    Route::get('documents/remove/{id}', array('as' => 'documents.destroy', 'uses' => 'DocumentsController@destroy'));
    Route::post('archive-documents', array('as' => 'documents.archive', 'uses' => 'DocumentsController@archive'));

});

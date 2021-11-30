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


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'chat'], function () {
    Route::get('/viewchat', array('as' => 'chat.viewchat', 'uses' => 'ChatController@index'));
    Route::get('/contacts', 'ContactsController@get');
    Route::get('/conversation/{id}', 'ContactsController@getMessagesFor');
    Route::post('/conversation/send', array('as' => 'chat.conversation-send', 'uses' => 'ContactsController@send'));
    Route::post('/conversation/save', array('as' => 'chat.conversation-save', 'uses' => 'ContactsController@saveForApp'));
    Route::get('/view-history', array('as' => 'chat.view-history', 'uses' => 'ChatHistoryController@index'));
    Route::get('/view-history-list', array('as' => 'chat.view-history.list', 'uses' => 'ChatHistoryController@getChatHistoryList'));
    Route::post('/contact/store', array('as' => 'chat.contact.store', 'uses' => 'ContactsController@store'));
    
    
});
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

//normal route
Route::group([
    'middleware' => ['web', 'permission:view_all_expense_claim|view_allocated_expense_claim'],
    'prefix' => 'expense',
    'namespace' => 'Modules\Expense\Http\Controllers'
], function () {
    Route::get('expense-dashboard/{viewmyexpense?}', array('as' => 'expense-dashboard.index', 'uses' => 'ExpenseDashboardController@index'));
    Route::get('expense-claims/list/{viewmyexpense?}/{startdate?}/{enddate?}/{status?}/{employee?}', array('as' => 'expense-claims.list', 'uses' => 'ExpenseDashboardController@getList'));
    Route::get('expense-claims/{id}', array('as' => 'expense-claims-single', 'uses' => 'ExpenseDashboardController@edit'));
    Route::post('expense-claims', array('as' => 'expense-claims.updateExpense', 'uses' => 'ExpenseDashboardController@update'));
    Route::get('expense-statements', array('as' => 'expense-statements.create', 'uses' => 'ExpenseSendStatementsController@index'));
    Route::post('expense-statements/store', array('as' => 'expense-statements.store', 'uses' => 'ExpenseSendStatementsController@store'));
    Route::get('expense-claims/count/{viewmyexpense?}/{startdate?}/{enddate?}/{employee?}', array('as' => 'expense-claims.getCounts', 'uses' => 'ExpenseDashboardController@getCounts'));
    Route::get('expense-statements-log', array('as' => 'expense-statements-log.show', 'uses' => 'ExpenseSendStatementsController@show'));
    Route::get('expense-statetement-log/list', array('as' => 'expense-statetement-log.list', 'uses' => 'ExpenseSendStatementsController@getList'));
    Route::get('expense-statements/destroy/{id}', array('as' => 'expense-statements.destroy', 'uses' => 'ExpenseSendStatementsController@destroy'));
    Route::get('expense-statements/mail', array('as' => 'expense-statements.mail', 'uses' => 'ExpenseDashboardController@expenseApprovalReminderMail'));
});

Route::group([
    'middleware' => ['web', 'permission:view_all_mileage_claim|view_allocated_mileage_claim'],
    'prefix' => 'mileage',
    'namespace' => 'Modules\Expense\Http\Controllers'
], function () {
    Route::get('mileage-dashboard/{viewmyexpense?}', array('as' => 'mileage-dashboard.index', 'uses' => 'MileageDashboardController@index'));
    Route::get('mileage-claims/list/{viewmyexpense?}/{startdate?}/{enddate?}/{status?}/{employee?}', array('as' => 'mileage-claims.list', 'uses' => 'MileageDashboardController@getList'));
    Route::get('mileage-claims/{id}', array('as' => 'mileage-claims-single', 'uses' => 'MileageDashboardController@edit'));
    Route::post('mileage-claims', array('as' => 'mileage-claims.updateMileageClaim', 'uses' => 'MileageDashboardController@update'));
    //  Route::get('expense-statements', array('as' => 'expense-statements.create', 'uses' => 'ExpenseSendStatementsController@index'));
    //  Route::post('expense-statements/store', array('as' => 'expense-statements.store', 'uses' => 'ExpenseSendStatementsController@store'));
    Route::get('mileage-claims/count/{viewmyexpense?}/{startdate?}/{enddate?}/{employee?}', array('as' => 'mileage-claims.getCounts', 'uses' => 'MileageDashboardController@getCounts'));
});

//masters section
Route::group([
    'middleware' => ['web', 'auth', 'permission:view_admin'],
    'prefix' => 'admin',
    'namespace' => 'Modules\Expense\Http\Controllers\Admin'
], function () {

    Route::group(['middleware' => ['permission:expense_masters']], function () {
        //Tax Master section
        Route::get('tax-master', array('as' => 'tax-master', 'uses' => 'TaxMasterController@index'));
        Route::get('tax-master/list', array('as' => 'tax-master.list', 'uses' => 'TaxMasterController@getList'));
        Route::post('tax-master/store', array('as' => 'tax-master.store', 'uses' => 'TaxMasterController@store'));
        Route::get('tax-master/single/{id}', array('as' => 'tax-master.single', 'uses' => 'TaxMasterController@getSingle'));
        Route::get('tax-master/show/{id}', array('as' => 'tax-master.show', 'uses' => 'TaxMasterController@taxMasterLogShow'));
        Route::get('tax-master/archive/{id}', array('as' => 'tax-master.archive', 'uses' => 'TaxMasterController@taxMasterLogShowTrashed'));
        Route::get('tax-master/getExpenseTracker', array('as' => 'tax-master.getExpenseTracker', 'uses' => 'TaxMasterController@getExpenseTracker'));
        Route::post('tax-master/expenseTrackerupdate', array('as' => 'tax-master.expenseTrackerupdate', 'uses' => 'TaxMasterController@expenseTrackerupdate'));
        Route::get('tax-master/destroy/{id}', array('as' => 'tax-master.destroy', 'uses' => 'TaxMasterController@destroy'));

        //Expense category section
        Route::name('expense-category')->get('expense-category', 'ExpenseCategoryLookupController@index');
        Route::get('expense-category/list', array('as' => 'expense-category.list', 'uses' => 'ExpenseCategoryLookupController@getList'));
        Route::get('expense-category/single/{id}', array('as' => 'expense-category.single', 'uses' => 'ExpenseCategoryLookupController@getSingle'));
        Route::post('expense-category/store', array('as' => 'expense-category.store', 'uses' => 'ExpenseCategoryLookupController@store'));
        Route::get('expense-category/destroy/{id}', array('as' => 'expense-category.destroy', 'uses' => 'ExpenseCategoryLookupController@destroy'));

        //Gl code section
        Route::name('view-gl-code')->get('view-gl-code', 'ExpenseGlController@index');
        Route::get('expense-gl/list', array('as' => 'expense-gl.list', 'uses' => 'ExpenseGlController@getList'));
        Route::get('expense-gl/single/{id}', array('as' => 'expense-gl.single', 'uses' => 'ExpenseGlController@getSingle'));
        Route::post('expense-gl/store', array('as' => 'expense-gl.store', 'uses' => 'ExpenseGlController@store'));
        Route::get('expense-gl/destroy/{id}', array('as' => 'expense-gl.destroy', 'uses' => 'ExpenseGlController@destroy'));

        //Cost center section
        Route::name('cost-center')->get('cost-center', 'CostCenterLookupController@index');
        Route::get('cost-center/list', array('as' => 'cost-center.list', 'uses' => 'CostCenterLookupController@getList'));
        Route::post('cost-center/store', array('as' => 'cost-center.store', 'uses' => 'CostCenterLookupController@store'));
        Route::get('cost-center/single/{id}', array('as' => 'cost-center.single', 'uses' => 'CostCenterLookupController@getSingle'));
        Route::get('cost-center/destroy/{id}', array('as' => 'cost-center.destroy', 'uses' => 'CostCenterLookupController@destroy'));

        //mileage section
        Route::name('mileage-reimbursement')->get('mileage-reimbursement', 'MileageReimbursementController@index');
        Route::post('mileage-reimbursement/add', array('as' => 'mileage-reimbursement.add', 'uses' => 'MileageReimbursementController@store'));
        Route::get('mileage-reimbursement/list', array('as' => 'expense-mileage-type.list', 'uses' => 'MileageReimbursementController@getList'));
        Route::get('mileage-reimbursement/single/{id}', array('as' => 'expense-mileage-type.single', 'uses' => 'MileageReimbursementController@getSingle'));
        Route::get('mileage-reimbursement/destroy/{id}', array('as' => 'expense-mileage-type.destroy', 'uses' => 'MileageReimbursementController@destroy'));

        //Expense Parent category section
        Route::name('expense-parent-category')->get('expense-parent-category', 'ParentExpenseCategoryController@index');
        Route::get('expense-parent-category/list', array('as' => 'expense-parent-category.list', 'uses' => 'ParentExpenseCategoryController@getList'));
        Route::post('expense-parent-category/store', array('as' => 'expense-parent-category.store', 'uses' => 'ParentExpenseCategoryController@store'));
        Route::get('expense-parent-category/single/{id}', array('as' => 'expense-parent-category.single', 'uses' => 'ParentExpenseCategoryController@getSingle'));
        Route::get('expense-parent-category/destroy/{id}', array('as' => 'expense-parent-category.destroy', 'uses' => 'ParentExpenseCategoryController@destroy'));

        //Expense Payment Mode section
        Route::name('expense-payment-mode')->get('expense-payment-mode', 'ExpensePaymentModeController@index');
        Route::get('expense-payment-mode/list', array('as' => 'expense-payment-mode.list', 'uses' => 'ExpensePaymentModeController@getList'));
        Route::post('expense-payment-mode/store', array('as' => 'expense-payment-mode.store', 'uses' => 'ExpensePaymentModeController@store'));
        Route::get('expense-payment-mode/single/{id}', array('as' => 'expense-payment-mode.single', 'uses' => 'ExpensePaymentModeController@getSingle'));
        Route::get('expense-payment-mode/destroy/{id}', array('as' => 'expense-payment-mode.destroy', 'uses' => 'ExpensePaymentModeController@destroy'));

        //expense settings
        Route::name('expense-settings')->get('expense-settings', 'ExpenseSettingsController@index');
        Route::post('expense-settings/add', array('as' => 'expense-settings.add', 'uses' => 'ExpenseSettingsController@store'));
        Route::post('expense-settings/store', array('as' => 'expense-settings.store', 'uses' => 'ExpenseSettingsController@store'));
    });
});

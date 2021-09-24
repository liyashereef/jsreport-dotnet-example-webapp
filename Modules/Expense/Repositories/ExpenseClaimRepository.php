<?php

namespace Modules\Expense\Repositories;

use Modules\Expense\Models\ExpenseClaim;
use Modules\Expense\Models\ExpenseAllowableForUser;
use Modules\Admin\Models\User;
use App\Repositories\MailQueueRepository;
use Modules\Expense\Models\ExpenseCategoryLookup;
use Modules\Expense\Models\ExpensePaymentMode;
use Modules\Expense\Models\ExpenseGlCode;
use Modules\Expense\Models\ExpenseCostCenterLookup;
use Modules\Expense\Models\ExpenseTaxMaster;
use Illuminate\Support\Facades\Auth;
use Modules\Expense\Models\ExpenseSettingsFinanceControllers;
use Modules\Expense\Models\ExpenseTaxMasterLog;
use Log;
use Modules\Expense\Models\ExpenseSendStatement;
use Exception;
use Modules\Admin\Repositories\UserRepository;
use Modules\Expense\Models\ExpenseEmailUpdate;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use App\Services\HelperService;
use Carbon\Carbon;

class ExpenseClaimRepository
{

    protected $model, $mailQueueRepository;

    public function __construct(
        ExpenseClaim $expenseClaim,
        MailQueueRepository $mailQueueRepository,
        ExpenseCategoryLookup $expenseCategoryLookup,
        ExpensePaymentMode $expensePaymentMode,
        ExpenseGlCode $expenseGlCode,
        ExpenseCostCenterLookup $expenseCostCenterLookup,
        ExpenseTaxMaster $expenseTaxMaster,
        ExpenseTaxMasterLog $expenseTaxMasterLog,
        UserRepository $userRepository,
        User $userModel,
        ExpenseEmailUpdate $expenseEmailUpdate,
        EmployeeAllocationRepository $employeeAllocationrepository
    ) {
        $this->model = $expenseClaim;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->expenseCategoryLookup = $expenseCategoryLookup;
        $this->expensePaymentMode = $expensePaymentMode;
        $this->expenseGlCode = $expenseGlCode;
        $this->expenseCostCenterLookup = $expenseCostCenterLookup;
        $this->expenseTaxMaster = $expenseTaxMaster;
        $this->expenseTaxMasterLog = $expenseTaxMasterLog;
        $this->userRepository = $userRepository;
        $this->usermodel = $userModel;
        $this->expenseEmailUpdate=$expenseEmailUpdate;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
    }

    public function getAll($viewmyexpense = 0, $startdate = false, $enddate = false, $status = false, $employee = false)
    {
        if ($viewmyexpense == 1) {
            if (\Auth::user()->can('view_allocated_expense_claim') || \Auth::user()->can('view_all_expense_claim')) {
                $view_my_expense = $this->model->with([
                    'expenseCategory',
                    'expenseGlCode',
                    'customer',
                    'attachmentDetails',
                    'created_user',
                    'created_user.trashedEmployee'
                ]);
                if ($startdate != '' && $enddate != '') {
                    $view_my_expense =  $view_my_expense
                    ->whereDate('created_at', '>=', $startdate)->whereDate('created_at', '<=', $enddate);
                }
                if ($status != '') {
                    $view_my_expense = $view_my_expense->where('status_id', $status);
                }
                $view_my_expense = $view_my_expense->where('created_by', \Auth::id());
                $view_my_expense = $view_my_expense->orderBy('created_at', 'desc')->get();
                return $view_my_expense;
            } else {
                $default = array();
                return $default;
            }
        } else {
            $status_arr = [1,2,3,4];
            /**
             * status
             * 1 = pending approval
             * 3 = approved
             * 4 = pending reimbursement
             * 5 = reimbured
             */
            $user = Auth::user();
            $financeControllers = ExpenseSettingsFinanceControllers::where('financial_controller', \Auth::id())->get();
            $approver = ExpenseAllowableForUser::where('reporting_to_id', \Auth::id())->get();
            if (\Auth::user()->can('view_allocated_expense_claim') || \Auth::user()->can('view_all_expense_claim')) {
                if ((count($approver) > 0) || $user->hasAnyPermission(['super_admin'])) {
                    $approver_view = $this->model->with([
                    'expenseCategory',
                    'expenseGlCode',
                    'customer',
                    'attachmentDetails',
                    'created_user',
                    'created_user.trashedEmployee'
                    ]);

                    if (!\Auth::user()->can('view_all_expense_claim')
                    && \Auth::user()->can('view_allocated_expense_claim')) {
                        $approver_view->whereHas('expenseAllowable', function ($q) {
                            $q->where('reporting_to_id', \Auth::user()->id);
                        });
                    }

                    if ($status != '') {
                        $approver_view =  $approver_view->where('status_id', $status);
                    }
                    if ($startdate != '' && $enddate != '') {
                        $approver_view =  $approver_view
                        ->whereDate('created_at', '>=', $startdate)->whereDate('created_at', '<=', $enddate);
                    }
                    if ($employee != '') {
                        $approver_view =  $approver_view->where('created_by', $employee);
                    }
                    $approver_view =  $approver_view->whereIn('status_id', $status_arr);
                    $approver_view = $approver_view->orderBy('created_at', 'desc')->get();
                } else {
                    $approver_view = collect();
                }

                if ((count($financeControllers) > 0) || $user->hasAnyPermission(['super_admin'])) {
                    $controller_view = $this->model->with([
                        'expenseCategory',
                        'expenseGlCode',
                        'customer',
                        'attachmentDetails',
                        'created_user',
                        'created_user.trashedEmployee'
                    ]);
                    $status_arr = [4,5];
                    $controller_view =  $controller_view->whereIn('status_id', $status_arr);

                    if ($status != '') {
                        $controller_view =  $controller_view->where('status_id', $status);
                    }
                    if ($startdate != '' && $enddate != '') {
                        $controller_view =  $controller_view
                        ->whereDate('created_at', '>=', $startdate)
                        ->whereDate('created_at', '<=', $enddate);
                    }
                    if ($employee != '') {
                        $controller_view =  $controller_view->where('created_by', $employee);
                    }
                    $controller_view = $controller_view->orderBy('created_at', 'desc')->get();
                } else {
                    $controller_view = collect();
                }

                return $merged = $approver_view->merge($controller_view);
            } else {
                $default = array();
                return $default;
            }
        }
    }



    public function expenseDataList($expenseClaimList)
    {
        $datatable_rows = array();
        foreach ($expenseClaimList as $key => $expenseList) {
            $eachRow["id"] = isset($expenseList->id) ? $expenseList->id : "--";
            $eachRow["financial_controller_id"] = isset($expenseList->financial_controller_id)
            ? $expenseList->financial_controller_id : "--";
            $eachRow["date"] = isset($expenseList->date) ? $expenseList->date->format('F d, Y') : "--";
            $eachRow["description"] = isset($expenseList->description) ? $expenseList->description : "--";
            $eachRow["expense_category"] = isset($expenseList->expenseCategory->name)
            ? $expenseList->expenseCategory->name : "--";
            $eachRow["gl_code"] = isset($expenseList->expenseGlCode->gl_code)
            ? $expenseList->expenseGlCode->gl_code : "--";
            $eachRow["no_attachment_reason"] = isset($expenseList->no_attachment_reason)
            ? $expenseList->no_attachment_reason : "--";
            $eachRow["project_number"] = isset($expenseList->customer->project_number)
            ? $expenseList->customer->project_number : "--";
            $eachRow["project_name"] = isset($expenseList->customer->client_name)
            ? $expenseList->customer->client_name : "--";
            $eachRow['status_id'] = isset($expenseList->status_id) ? $expenseList->status_id : "--";
            array_push($datatable_rows, $eachRow);
        }
        return $datatable_rows;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function saveExpenseClaim($request)
    {
        $date = \Carbon::now();

        $expenseClaim = new ExpenseClaim();

        //$expenseClaim->date = $date->toDateString();
        $tip= ($request->tip)?$request->tip:0;
        $expenseClaim->date = $request->date;
        $expenseClaim->amount = ($request->amount)+$tip;
        $expenseClaim->attachment_id = $request->attachment_id;
        $expenseClaim->expense_category_id = $request->expense_category_id;
        $expenseClaim->reimbursed = 0;
        $expenseClaim->status_id = 1;
        $expenseClaim->created_by = \Auth::id();
        $expenseClaim->expense_gl_codes_id = $request->expense_gl_codes_id;
        $expenseClaim->description = $request->description;
        $expenseClaim->no_attachment_reason = $request->no_attachment_reason;
        $expenseClaim->project_id = $request->project_id;
        $expenseClaim->cost_center_id = $request->cost_center_id;
        $expenseClaim->participants = $request->participants;
        $expenseClaim->claim_reimbursement = $request->claim_reimbursement;
        $expenseClaim->payment_mode_id = $request->payment_mode_id;
        $expenseClaim->attachment = ($request->attachment)?$request->attachment:0;
        $expenseClaim->billable = ($request->billable)?$request->billable:0;
        $expenseClaim->tax_percentage = $request->tax_percentage;
        $expenseClaim->tax_amount = $request->tax_amount;
        $expenseClaim->tip = $tip;

        $expenseClaim->save();

            $lastSavedId = $expenseClaim->id;

            $toGetCreatedById = ExpenseClaim::where('id', $lastSavedId)->first();

            $userId = $toGetCreatedById->created_by;

            $attachmentId = $toGetCreatedById->attachment_id;

            $reportingToId = ExpenseAllowableForUser::where('user_id', $userId)->first();
        if (is_null($reportingToId) || is_null($reportingToId->reporting_to_id)) {
            throw new Exception("No reporting person found");
        }

            $reporterId = $reportingToId->reporting_to_id;


            $toGetUserEmail = User::where('id', $reporterId)->first();
            $claimCreatedBy = User::where('id', \Auth::id())->first();
            $claimCreatedByName = $claimCreatedBy->first_name.' '.(isset($claimCreatedBy->last_name)
            ?$claimCreatedBy->last_name : '');
            $to   = $toGetUserEmail->email;
            $attachment_id = ($attachmentId)?$attachmentId:'';
            $model_name ='Expense claim submit api';
            $subject = 'New Expense claim submitted by '.$claimCreatedByName;
            $message = 'A new claim has been submitted by '.$claimCreatedByName
            .'. Please log into your CGL360 account to review the transaction. ';
            $mail_queue = $this->mailQueueRepository
            ->storeMail($to, $subject, $message, $model_name, null, null, null, null, null, $attachment_id);
            //$mail_queue = $this->mailQueueRepository
            //->storeMail($to,$subject,$message,null,null,null,null,null,$attachment_id,$model_name);
    }

    public function getSingle($id)
    {
        $result = $this->model->with([
            'created_user',
            'created_user.employee',
            'customer',
            'attachmentDetails',
            'approved_by_user',
            'finance_controller',
            'status',
            'expenseCategory'
        ])->where('id', $id)->take(1)->first();
        return $result;
    }


    public function getExpenseCounts($viewmyexpense, $startdate, $enddate, $employee = null)
    {
        if ($viewmyexpense == 1) {
            $status_array = [1,2,3,4,5];
            if (\Auth::user()->can('view_allocated_expense_claim') || \Auth::user()->can('view_all_expense_claim')) {
                $view_my_expense =  ExpenseClaim::select(
                    \DB::raw("SUM(amount) as total_amount"),
                    \DB::raw("COUNT(*) as total_count"),
                    'status_id'
                )
                    ->whereDate('date', '>=', $startdate)
                    ->whereDate('date', '<=', $enddate);

                if ($startdate != '' && $enddate != '') {
                    $view_my_expense =  $view_my_expense
                    ->whereDate('date', '>=', $startdate)
                    ->whereDate('date', '<=', $enddate);
                }
                $view_my_expense = $view_my_expense->where('created_by', \Auth::id());
                $view_my_expense =  $view_my_expense->whereIn('status_id', $status_array);
                $view_my_expense = $view_my_expense->groupBy('status_id')->get()->toArray();
                return $view_my_expense;
            } else {
                $default = array();
                return $default;
            }
        } else {
            $status_arr = [1,2,3,4];
            $user = Auth::user();
            $financeControllers = ExpenseSettingsFinanceControllers::where('financial_controller', \Auth::id())->get();
            $approver = ExpenseAllowableForUser::where('reporting_to_id', \Auth::id())->get();
            if (\Auth::user()->can('view_allocated_expense_claim') || \Auth::user()->can('view_all_expense_claim')) {
                if ((count($approver) > 0) || $user->hasAnyPermission(['super_admin'])) {
                    $approver_view =  ExpenseClaim::select(
                        \DB::raw("SUM(amount) as total_amount"),
                        \DB::raw("COUNT(*) as total_count"),
                        'status_id'
                    )->whereDate('date', '>=', $startdate)
                    ->whereDate('date', '<=', $enddate);

                    if (!\Auth::user()->can('view_all_expense_claim') && \Auth::user()
                    ->can('view_allocated_expense_claim')) {
                        $approver_view->whereHas('expenseAllowable', function ($q) {
                            $q->where('reporting_to_id', \Auth::user()->id);
                        });
                    }
                    if ($startdate != '' && $enddate != '') {
                        $approver_view =  $approver_view
                        ->whereDate('date', '>=', $startdate)
                        ->whereDate('date', '<=', $enddate);
                    }
                    if ($employee != '') {
                        $approver_view =  $approver_view->where('created_by', $employee);
                    }
                    $approver_view =  $approver_view->whereIn('status_id', $status_arr);
                    $approver_view = $approver_view->groupBy('status_id')->get()->toArray();
                } else {
                    $approver_view = array();
                }

                if ((count($financeControllers) > 0) || $user->hasAnyPermission(['super_admin'])) {
                    $controller_view =  ExpenseClaim::select(
                        \DB::raw("SUM(amount) as total_amount"),
                        \DB::raw("COUNT(*) as total_count"),
                        'status_id'
                    )->whereDate('date', '>=', $startdate)
                    ->whereDate('date', '<=', $enddate);
                    $status_arr = [4,5];
                    $controller_view =  $controller_view->whereIn('status_id', $status_arr);
                    if ($startdate != '' && $enddate != '') {
                        $controller_view =  $controller_view
                        ->whereDate('date', '>=', $startdate)
                        ->whereDate('date', '<=', $enddate);
                    }
                    if ($employee != '') {
                        $controller_view =  $controller_view->where('created_by', $employee);
                    }
                    $controller_view = $controller_view->groupBy('status_id')->get()->toArray();
                } else {
                    $controller_view = array();
                }


                    return $merged = array_merge($approver_view, $controller_view);
            } else {
                $default = array();
                return $default;
            }
        }
    }


    public function updateExpenseClaim($request)
    {
        $expense_details = $this->getSingle($request->get('expense_id'));
        $expense = [
            'expense_gl_codes_id' => $request->get('gl_code_id'),
            'cost_center_id' => $request->get('cost_center_id')
        ];
        if ($request->get('status_id') == 2 || $request->get('status_id') == 3) {
            $expense['approved_by'] =  \Auth::user()->id;
            $expense['approver_comments'] =  $request->get('approver_comments');
            if ($request->get('status_id') == 2) {
                $expense['status_id'] =  2;
                /* send mail begin*/
                $toGetUserEmail = User::where('id', $expense_details->created_by)->first();
                $claimRejectedBy = User::where('id', \Auth::id())->first();
                $claimRejectedByName = $claimRejectedBy->first_name . ' ' . (isset($claimRejectedBy->last_name)
                    ? $claimRejectedBy->last_name : '');
                $claimTotalAmount = isset($expense_details->amount) ? $expense_details->amount : '';
                $claimRejectedDate = Carbon::now()->format('Y-m-d');
                $to   = $toGetUserEmail->email;
                $model_name = 'Expense Rejected';
                $helper_variable = array(
                    '{claimRejectedByName}' => $claimRejectedByName,
                    '{receiverFullName}' => $claimRejectedByName,
                    '{claimTotalAmount}' => $claimTotalAmount,
                    '{claimRejectedDate}' => $claimRejectedDate,
                );
                $mail_queue = $this->mailQueueRepository
                    ->prepareMailTemplate(
                        "expense_claim_rejection_email",
                        null,
                        $helper_variable,
                        $model_name,
                        $requestor = 0,
                        $assignee = 0,
                        $from = null,
                        $cc = null,
                        $bcc = null,
                        $mail_time = null,
                        $created_by = null,
                        $attachment_id = null,
                        $to
                    );
                /* send mail end*/
            } elseif ($request->get('status_id') == 3) {
                if ($expense_details->claim_reimbursement == 1) {
                    $expense['status_id'] =  4;
                    /* send mail begin*/
                    $toGetUserEmail = User::where('id', $expense_details->created_by)->first();
                    $claimApprovedBy = User::where('id', \Auth::id())->first();
                    $claimApprovedByName = $claimApprovedBy->first_name . ' ' . (isset($claimApprovedBy->last_name)
                        ? $claimApprovedBy->last_name : '');
                    $claimTotalAmount = isset($expense_details->amount) ? $expense_details->amount : '';
                    $claimApprovedDate = Carbon::now()->format('Y-m-d');
                    $to   = $toGetUserEmail->email;
                    $model_name = 'Expense Approved';
                    $helper_variable = array(
                        '{receiverFullName}' => $claimApprovedByName,
                        '{claimApprovedByName}' => $claimApprovedByName,
                        '{claimTotalAmount}' => $claimTotalAmount,
                        '{claimApprovedDate}' => $claimApprovedDate,
                    );
                    $mail_queue = $this->mailQueueRepository
                        ->prepareMailTemplate(
                            "expense_claim_reimbursement_approval_email",
                            null,
                            $helper_variable,
                            $model_name,
                            $requestor = 0,
                            $assignee = 0,
                            $from = null,
                            $cc = null,
                            $bcc = null,
                            $mail_time = null,
                            $created_by = null,
                            $attachment_id = null,
                            $to
                        );
                    /* send mail end*/

                    /* send mail to financial controller begin*/
                    $claimCreatedBy = User::where('id', $expense_details->created_by)->first();
                    $claimCreatedByName = $claimCreatedBy->first_name . ' ' . (isset($claimCreatedBy->last_name)
                        ? $claimCreatedBy->last_name : '');
                    $claimSubmittedDate = Carbon::now()->format('Y-m-d');
                    $claimApprovedBy = User::where('id', \Auth::id())->first();
                    $claimApprovedByName = $claimApprovedBy->first_name . ' ' . (isset($claimApprovedBy->last_name)
                        ? $claimApprovedBy->last_name : '');
                    $claimTotalAmount = isset($expense_details->amount) ? $expense_details->amount : '';
                    $claimApprovedDate = Carbon::now()->format('Y-m-d');
                    $helper_variable = array(
                        '{receiverFullName}' => $claimApprovedByName,
                        '{claimCreatedByName}' => $claimCreatedByName,
                        '{claimApprovedByName}' => $claimApprovedByName,
                        '{claimTotalAmount}' => $claimTotalAmount,
                        '{claimApprovedDate}' => $claimApprovedDate,
                        '{claimSubmittedDate}' => $claimSubmittedDate,
                    );
                    $finance_controllers = ExpenseSettingsFinanceControllers::pluck('financial_controller')->toArray();
                    foreach ($finance_controllers as $eachUser) {
                        $toGetUserEmail = User::where('id', $eachUser)->first();
                        $to   = $toGetUserEmail->email;
                        $model_name = 'Expense claim request';
                        $mail_queue = $this->mailQueueRepository
                            ->prepareMailTemplate(
                                "expense_claim_submission_email",
                                null,
                                $helper_variable,
                                $model_name,
                                $requestor = 0,
                                $assignee = 0,
                                $from = null,
                                $cc = null,
                                $bcc = null,
                                $mail_time = null,
                                $created_by = null,
                                $attachment_id = null,
                                $to
                            );
                    }
                    /* send mail end*/
                } else {
                    $expense['status_id'] =  3;
                    /* send mail begin*/
                    $toGetUserEmail = User::where('id', $expense_details->created_by)->first();
                    $claimApprovedBy = User::where('id', \Auth::id())->first();
                    $claimApprovedByName = $claimApprovedBy->first_name . ' ' . (isset($claimApprovedBy->last_name)
                        ? $claimApprovedBy->last_name : '');
                    $claimTotalAmount = isset($expense_details->amount) ? $expense_details->amount : '';
                    $claimApprovedDate = Carbon::now()->format('Y-m-d');
                    $to   = $toGetUserEmail->email;
                    $model_name = 'Expense Approved';
                    $helper_variable = array(
                        '{receiverFullName}' => $claimApprovedByName,
                        '{claimApprovedByName}' => $claimApprovedByName,
                        '{claimTotalAmount}' => $claimTotalAmount,
                        '{claimApprovedDate}' => $claimApprovedDate,
                    );
                    $mail_queue = $this->mailQueueRepository
                        ->prepareMailTemplate(
                            "expense_claim_approval_email",
                            null,
                            $helper_variable,
                            $model_name,
                            $requestor = 0,
                            $assignee = 0,
                            $from = null,
                            $cc = null,
                            $bcc = null,
                            $mail_time = null,
                            $created_by = null,
                            $attachment_id = null,
                            $to
                        );
                    /* send mail end*/
                }
            }
        } elseif ($request->get('status_id') == 4 || $request->get('status_id') == 5) {
            $expense['status_id'] =  $request->get('status_id');
            $expense['finance_comments'] =  $request->get('finance_comments');
            $expense['financial_controller_id'] =  \Auth::user()->id;
            $expense['payment_mode_id'] =  $request->get('mode_of_payment_id');
            if ($request->get('status_id') == 5) {
                /* send mail begin*/
                $toGetUserEmail = User::where('id', $expense_details->created_by)->first();
                $claimApprovedBy = User::where('id', \Auth::id())->first();
                $claimApprovedByName = $claimApprovedBy->first_name . ' '
                    . (isset($claimApprovedBy->last_name) ? $claimApprovedBy->last_name : '');
                $claimTotalAmount = isset($expense_details->amount) ? $expense_details->amount : '';
                $claimApprovedDate = Carbon::now()->format('Y-m-d');
                $to   = $toGetUserEmail->email;
                $model_name = 'Expense Approved';
                $helper_variable = array(
                    '{receiverFullName}' => $claimApprovedByName,
                    '{claimApprovedByName}' => $claimApprovedByName,
                    '{claimTotalAmount}' => $claimTotalAmount,
                    '{claimApprovedDate}' => $claimApprovedDate,
                );
                $mail_queue = $this->mailQueueRepository
                    ->prepareMailTemplate(
                        "expense_claim_approval_email",
                        null,
                        $helper_variable,
                        $model_name,
                        $requestor = 0,
                        $assignee = 0,
                        $from = null,
                        $cc = null,
                        $bcc = null,
                        $mail_time = null,
                        $created_by = null,
                        $attachment_id = null,
                        $to
                    );
                /* send mail end*/
            }
        }
        $this->model->find($request->get('expense_id'))->update($expense);
    }
// if all tax is archieved category will not shown
    public function getCategoryList()
    {
        // $result = $this->expenseCategoryLookup
        //->select('id', 'name','short_name','is_category_taxable','tax_id','description')
        //                 ->where('deleted_at', NULL)->get();
        // return $result;
        $date = \Carbon::now();
        $effectiveDate = $date->toDateString();
        //dd($effectiveDate);
        $ids=ExpenseTaxMasterLog::where('status', 0)->where('effective_from_date', '<=', $effectiveDate)
                ->where(function ($q) use ($effectiveDate) {
                    $q->where('effective_end_date', null);
                    $q->orwhere('effective_end_date', '<=', $effectiveDate);
                })->pluck('tax_master_id');
        //dd($ids);
        $result = $this->expenseCategoryLookup->select(
            'id',
            'name',
            'short_name',
            'is_category_taxable',
            'is_tip_enabled',
            'tax_id',
            'description'
        )
                    ->where(function ($query) use ($ids) {
                        $query->wherein('tax_id', $ids);
                        $query->orwhere('is_category_taxable', 0);
                    })
                   ->where('deleted_at', null)->get();
                    return $result;
    }

    public function getPaymentList()
    {
        $result = $this->expensePaymentMode
        ->select('id', 'mode_of_payment', 'reimbursement')
        ->where('deleted_at', null)
        ->orderBy('mode_of_payment', 'asc')->get();
        return $result;
    }

    public function getGlCodeList()
    {
        $result = $this->expenseGlCode
        ->select('id', 'gl_code')
        ->where('deleted_at', null)
        ->orderBy('gl_code', 'asc')->get();
        return $result;
    }

    public function getCostCenterList()
    {
        $result = $this->expenseCostCenterLookup
        ->select('id', 'center_number')
        ->where('deleted_at', null)
        ->orderBy('center_number', 'asc')->get();
        return $result;
    }
    public function getTaxList()
    {
        $result = $this->expenseTaxMaster
        ->select('id', 'name')
        ->where('deleted_at', null)
        ->orderBy('name', 'asc')->get();
        return $result;
    }

    public function getTaxListLog()
    {
        $result = $this->expenseTaxMasterLog
        ->select('id', 'tax_master_id', 'tax_percentage', 'effective_from_date')
        ->where('status', '0')->where('deleted_at', null)->get();
        return $result;
    }
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.expense-send-statements'), null);
    }
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = ExpenseSendStatement::where('attachment_id', $file_id)->first();
        return array(config('globals.expense-send-statements'), null);
    }

    public function expenseSendLog()
    {
        $query = array();
        $user = Auth::id();
        $query = ExpenseSendStatement::where('financial_controller_id', $user)
        ->with(['attachment','user'])->latest()->withTrashed()->orderBy('created_at', 'desc')->get();
//dd($query[0]);
        return $query;
    }

    public function prepareDataForExpenseClaim($expense_list)
    {

        $datatable_rows = array();
        foreach ($expense_list as $key => $each_list) {
            $each_row["id"]              = $each_list->id;
            $each_row["transaction_date"] = isset($each_list->date)
            ?$each_list->date->toFormattedDateString():"--";
            $last_name=($each_list->created_user->last_name)?' '.$each_list->created_user->last_name:'';
            $employee_no=isset($each_list->created_user->trashedEmployee->employee_no)
            ? ' ('.$each_list->created_user->trashedEmployee->employee_no.')' : '';
            $each_row["name"]         = $each_list->created_user->first_name.$last_name.$employee_no;
            $each_row["description"]    = $each_list->description;
            $each_row["amount"]    = '$'.($each_list->amount ?? '0');
            $each_row["attachment_id"]    = $each_list->attachment_id ?? '';
            $each_row["billable"]        = ($each_list->billable == 1) ? 'Yes' : 'No';
            if ($each_list->status_id == 1) {
                $status='Pending Approval';
            } elseif ($each_list->status_id == 2) {
                $status='Rejected';
            } elseif ($each_list->status_id == 3) {
                $status='Approved';
            } elseif ($each_list->status_id == 4) {
                $status='Pending Reimbursement';
            } elseif ($each_list->status_id == 5) {
                $status='Reimbursed';
            }
            $each_row["status_id"]    = $status ?? '';
            $each_row["created_at"] = isset($each_list->created_at)
            ?$each_list->created_at->toFormattedDateString():"--";
            $each_row["ordering_created_at"] = isset($each_list->created_at)
            ?$each_list->created_at->format('Y-m-d H:i'):"--";

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function employeeLookUps()
    {
        $user_list = array();
        if (\Auth::user()->can('view_all_expense_claim')) {
            $user_list = $this->userRepository->getUserLookup(null, ['admin','super_admin'], null, true, null, true)
                ->orderBy('first_name', 'asc')
            ->get();
        } else {
            $reported_to_id = ExpenseAllowableForUser::where('reporting_to_id', \Auth::user()->id)->pluck('user_id')->toArray();
            $user_list = $this->usermodel
            ->whereIn('id', $reported_to_id)
            ->orderBy('first_name', 'asc')
            ->get();
        }
        return $user_list;
    }

    public function expenseApprovalReminderMail()
    {
         $onboardingList = $this->expenseEmailUpdate->first();
        if (isset($onboardingList)) {
            $pendingTask = $this->model
                ->where('status_id', '=', 1) //Not approved
                 ->whereRaw('DATEDIFF("'.date("Y-m-d") . '",date)>='.$onboardingList->interval)
                ->get();
            foreach ($pendingTask as $eachTask) {
                $receiver = ExpenseAllowableForUser::with('reportingUser.employee', 'user.employee')->where('user_id', $eachTask->created_by)->first();
                $helper_variable = array(
                    '{receiverFullName}' => HelperService::sanitizeInput($receiver->reportingUser->full_name),
                    '{receiverEmployeeNumber}' => HelperService::sanitizeInput($receiver->reportingUser->employee->employee_no),
                    '{submittedByEmployeeName}' => HelperService::sanitizeInput($receiver->user->full_name),
                    '{submittedByEmployeeNumber}' => HelperService::sanitizeInput($receiver->user->employee->employee_no),
                    '{submittedDate}' => $eachTask->date,
                    '{totalAmount}' => $eachTask->amount,
                );
                $emailResult = $this->mailQueueRepository
                    ->prepareMailTemplate(
                        "expense_approve_notification_remainder",
                        null,
                        $helper_variable,
                        "Modules\Expense\Models\ExpenseClaim",
                        0,
                        $receiver->reporting_to_id
                    );
            }
        }
        return true;
    }
}

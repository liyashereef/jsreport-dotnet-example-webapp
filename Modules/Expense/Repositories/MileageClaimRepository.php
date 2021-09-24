<?php

namespace Modules\Expense\Repositories;

use Modules\Expense\Models\MileageClaim;
use Modules\Admin\Models\User;
use App\Repositories\MailQueueRepository;
use Illuminate\Support\Facades\Auth;
use Modules\Expense\Models\ExpenseSettingsFinanceControllers;
use Log;
use Modules\Expense\Models\ExpenseMileageReimbursementFlatRate;
use Modules\Expense\Models\ExpenseAllowableForUser;
use Modules\Vehicle\Models\Vehicle;
use Exception;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;

class MileageClaimRepository
{

    protected $model, $mailQueueRepository, $vehicle, $flatRate;

    public function __construct(
        MileageClaim $mileageClaim,
        MailQueueRepository $mailQueueRepository,
        Vehicle $vehicle,
        ExpenseMileageReimbursementFlatRate $flatRate,
        UserRepository $userRepository,
        User $userModel,
        EmployeeAllocationRepository $employeeAllocationrepository
    ) {
        $this->model = $mileageClaim;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->vehicle = $vehicle;
        $this->flatRate = $flatRate;
        $this->userRepository = $userRepository;
        $this->usermodel = $userModel;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
    }

    public function getAll($viewmyexpense = 0, $startdate = false, $enddate = false, $status = false, $employee = false)
    {
        if ($viewmyexpense == 1) {
            if ((\Auth::user()->can('view_allocated_mileage_claim')
            || \Auth::user()->can('view_all_mileage_claim'))
            || $user->hasAnyPermission(['super_admin'])) {
                $view_my_expense = $this->model->with(['customer','created_user','created_user.trashedEmployee']);
                if ($status != '') {
                    $view_my_expense =  $view_my_expense->where('status_id', $status);
                }
                if ($startdate != '' && $enddate != '') {
                    $view_my_expense =  $view_my_expense
                    ->whereDate('created_at', '>=', $startdate)
                    ->whereDate('created_at', '<=', $enddate);
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
            $user = Auth::user();
            $financeControllers = ExpenseSettingsFinanceControllers::where('financial_controller', \Auth::id())->get();
            $approver = ExpenseAllowableForUser::where('reporting_to_id', \Auth::id())->get();
            if ((\Auth::user()->can('view_allocated_mileage_claim')
            || \Auth::user()->can('view_all_mileage_claim'))
            || $user->hasAnyPermission(['super_admin'])) {
                if ((count($approver) > 0) || $user->hasAnyPermission(['super_admin'])) {
                    $approver_view = $this->model->with(['customer','created_user','created_user.trashedEmployee']);

                    if (!\Auth::user()->can('view_all_mileage_claim') && \Auth::user()
                    ->can('view_allocated_mileage_claim')) {
                        $approver_view->whereHas('expenseAllowable', function ($q) {
                            $q->where('reporting_to_id', \Auth::user()->id);
                        });
                    }

                    if ($status != '') {
                        $approver_view =  $approver_view->where('status_id', $status);
                    }
                    if ($startdate != '' && $enddate != '') {
                        $approver_view =  $approver_view->whereDate('created_at', '>=', $startdate)
                        ->whereDate('created_at', '<=', $enddate);
                    }
                    if ($employee != '') {
                        $approver_view =  $approver_view->where('created_by', $employee);
                    }
                    $approver_view =  $approver_view->whereIn('status_id', $status_arr);
                    $approver_view = $approver_view->orderBy('created_at', 'desc')->get();
                } else {
                    $approver_view = collect();
                }

                if ((count($financeControllers) > 0) || $user->hasAnyPermission([ 'super_admin'])) {
                    $controller_view = $this->model->with(['customer','created_user','created_user.trashedEmployee']);
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



    public function get($id)
    {
        return $this->model->find($id);
    }

    public function saveMileageClaim($request)
    {
    }

    public function getSingle($id)
    {

        $result = $this->model->with([
            'created_user',
            'created_user.employee',
            'customer',
            'approved_by_user',
            'finance_controller',
            'status',
            'vehicle'
            ])->where('id', $id)->take(1)->first();

        return $result;
    }


    public function getMileageCounts($viewmyexpense, $startdate, $enddate, $employee = null)
    {
        if ($viewmyexpense == 1) {
            $status_array = [1,2,3,4,5];
            if (\Auth::user()->can('view_allocated_mileage_claim') || \Auth::user()->can('view_all_mileage_claim')) {
                $view_my_expense = MileageClaim::select(
                    \DB::raw("SUM(amount) as total_amount"),
                    \DB::raw("COUNT(*) as total_count"),
                    'status_id'
                )->whereDate('date', '>=', $startdate)
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

            if (\Auth::user()->can('view_allocated_mileage_claim') || \Auth::user()->can('view_all_mileage_claim')) {
                if ((count($approver) > 0) || $user->hasAnyPermission(['super_admin'])) {
                    $approver_view = MileageClaim::select(
                        \DB::raw("SUM(amount) as total_amount"),
                        \DB::raw("COUNT(*) as total_count"),
                        'status_id'
                    )->whereDate('date', '>=', $startdate)
                    ->whereDate('date', '<=', $enddate);

                    if (!\Auth::user()->can('view_all_mileage_claim')
                    && \Auth::user()->can('view_allocated_mileage_claim')) {
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
                    $controller_view = MileageClaim::select(
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


    public function updateMileageClaim($request)
    {
        $mileage_details = $this->getSingle($request->get('mileage_id'));
        if ($request->get('status_id') == 2 || $request->get('status_id') == 3) {
            $mileage['approved_by'] =  \Auth::user()->id;
            $mileage['approver_comments'] =  $request->get('approver_comments');
            if ($request->get('status_id') == 2) {
                $mileage['status_id'] =  2;
                 /* send mail begin*/
                 $toGetUserEmail = User::where('id', $mileage_details->created_by)->first();
                 $claimRejectedBy = User::where('id', \Auth::id())->first();
                 $claimRejectedByName = $claimRejectedBy->first_name.' '.(isset($claimRejectedBy->last_name)
                 ?$claimRejectedBy->last_name : '');
                 $to   = $toGetUserEmail->email;
                 $model_name = 'Mileage Claim Rejected';
                 $subject = 'Mileage claim has been rejected';
                 $message = 'Mileage claim has been rejected by '
                 .$claimRejectedByName.'. Please log into your CGL360 account to review the transaction. ';
                 $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                 /* send mail end*/
            } elseif ($request->get('status_id')==3) {
                if ($mileage_details->claim_reimbursement==1) {
                    $mileage['status_id'] =  4;
                    /* send mail begin*/
                    $toGetUserEmail = User::where('id', $mileage_details->created_by)->first();
                    $claimApprovedBy = User::where('id', \Auth::id())->first();
                    $claimApprovedByName = $claimApprovedBy->first_name.' '.(isset($claimApprovedBy->last_name)
                    ?$claimApprovedBy->last_name : '');
                    $to   = $toGetUserEmail->email;
                    $model_name = 'Mileage Approved';
                    $subject = 'Mileage claim has been approved';
                    $message = 'Mileage claim has been approved by '.$claimApprovedByName
                    .' and forwarded to Financial controller. Please log into your CGL360 account to review the transaction. ';
                    $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                    /* send mail end*/

                    /* send mail to financial controller begin*/
                    $finance_controllers = ExpenseSettingsFinanceControllers::pluck('financial_controller')->toArray();
                    foreach ($finance_controllers as $eachUser) {
                        $toGetUserEmail = User::where('id', $eachUser)->first();
                        $claimCreatedBy = User::where('id', $mileage_details->created_by)->first();
                        $claimCreatedByName = $claimCreatedBy->first_name.' '.(isset($claimCreatedBy->last_name)
                        ?$claimCreatedBy->last_name : '');
                        $claimApprovedBy = User::where('id', \Auth::id())->first();
                        $claimApprovedByName = $claimApprovedBy->first_name.' '.(isset($claimApprovedBy->last_name)
                        ?$claimApprovedBy->last_name : '');
                        $to   = $toGetUserEmail->email;
                        $model_name = 'Mileage claim request';
                        $subject = 'Mileage claim has been submitted';
                        $message = 'A new claim has been submitted by '.$claimCreatedByName.' and approved by '
                        .$claimApprovedByName.'. Please log into your CGL360 account to review the transaction. ';
                        $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                    }
                    /* send mail end*/
                } else {
                    $mileage['status_id'] =  3;
                   /* send mail begin*/
                    $toGetUserEmail = User::where('id', $mileage_details->created_by)->first();
                    $claimApprovedBy = User::where('id', \Auth::id())->first();
                    $claimApprovedByName = $claimApprovedBy->first_name.' '.(isset($claimApprovedBy->last_name)
                    ?$claimApprovedBy->last_name : '');
                    $to   = $toGetUserEmail->email;
                    $model_name = 'Mileage Approved';
                    $subject = 'Mileage claim has been approved';
                    $message = 'Mileage claim has been approved by '.$claimApprovedByName
                    .'. Please log into your CGL360 account to review the transaction. ';
                    $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                   /* send mail end*/
                }
            }
        } elseif ($request->get('status_id') == 4 || $request->get('status_id') == 5) {
            $mileage['status_id'] =  $request->get('status_id');
            $mileage['finance_comments'] =  $request->get('finance_comments');
            $mileage['financial_controller_id'] =  \Auth::user()->id;
            if ($request->get('status_id') == 5) {
                /* send mail begin*/
                $toGetUserEmail = User::where('id', $mileage_details->created_by)->first();
                $claimApprovedBy = User::where('id', \Auth::id())->first();
                $claimApprovedByName = $claimApprovedBy->first_name.' '.(isset($claimApprovedBy->last_name)
                ?$claimApprovedBy->last_name : '');
                $to   = $toGetUserEmail->email;
                $model_name = 'Mileage Approved';
                $subject = 'Mileage claim has been approved';
                $message = 'Mileage claim has been approved by '
                .$claimApprovedByName.'. Please log into your CGL360 account to review the transaction. ';
                $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
                /* send mail end*/
            }
        }
        $this->model->find($request->get('mileage_id'))->update($mileage);
    }

    public function saveMileageClaims($request)
    {
        $date = \Carbon::now();

        $mileageClaim = new MileageClaim();

        //$mileageClaim->date = $date->toDateString();
        $mileageClaim->date = $request->date;
        $mileageClaim->description = $request->description;
        $mileageClaim->starting_location = $request->starting_location;
        $mileageClaim->destination = $request->destination;
        $mileageClaim->starting_km = $request->starting_km;
        $mileageClaim->ending_km = $request->ending_km;
        $mileageClaim->vehicle_type = $request->vehicle_type;
        $mileageClaim->vehicle_id = $request->vehicle_id;
        $mileageClaim->billable = ($request->billable)?$request->billable:0;
        $mileageClaim->associate_with_client = $request->associate_with_client;
        //project id is based on client
        $mileageClaim->project_id = $request->project_id;
        $mileageClaim->claim_reimbursement = $request->claim_reimbursement;
        $mileageClaim->status_id = 1;
        $mileageClaim->created_by = \Auth::id();

        $mileageClaim->amount = $request->amount;
        $mileageClaim->total_km = $request->total_km;
        $mileageClaim->rate = $request->rate;

        $mileageClaim->save();

            $lastSavedId = $mileageClaim->id;

            $toGetCreatedById = MileageClaim::where('id', $lastSavedId)->first();

            $userId = $toGetCreatedById->created_by;

            //$startingKm = $toGetCreatedById->starting_km;
            //$endingKm = $toGetCreatedById->ending_km;

            //$totalKm = $endingKm - $startingKm;

            //$flatRate = ExpenseMileageReimbursementFlatRate::where('is_active',1)->latest()->first();

            //$newFlatRate=$flatRate->flat_rate;

            //$amount = $totalKm * $newFlatRate;

            // $mileageClaimUpdate = MileageClaim::where('id', $lastSavedId)->first();

            //   $mileageClaimUpdate->fill([
            //      'amount' => $amount,
            //      'total_km' => $totalKm
            //  ]);
            //  $mileageClaimUpdate->save();

            $reportingToId = ExpenseAllowableForUser::where('user_id', $userId)->first();
        if (is_null($reportingToId)) {
            throw new Exception("No reporting person found");
        }
            $reporterId = $reportingToId->reporting_to_id;

            $toGetUserEmail = User::where('id', $reporterId)->first();
            $claimCreatedBy = User::where('id', \Auth::id())->first();
            $claimCreatedByName = $claimCreatedBy->first_name.' '.(isset($claimCreatedBy->last_name)
            ?$claimCreatedBy->last_name : '');
            $to   = $toGetUserEmail->email;

            $model_name ='Mileage claim submit api';
            $subject = 'New Mileage claim submitted by '.$claimCreatedByName;
            $message = 'A new claim has been submitted by '.$claimCreatedByName
            .'. Please log into your CGL360 account to review the transaction. ';

            //$subject = 'Mileage claim reimbursement';
            //$message = 'Hi, Mileage claim reimbursement.';
            $mail_queue = $this->mailQueueRepository
            ->storeMail($to, $subject, $message, $model_name, null, null, null, null, null, null);
    }

    public function getVehicleList()
    {
        $result = $this->vehicle->select('id', 'make', 'model', 'number')
        ->where('deleted_at', null)->orderBy('model', 'asc')->get();
        return $result;
    }

    public function getFlatRate()
    {
        $result = $this->flatRate->select('flat_rate')->where('is_active', 1)->latest()->first();
        // if (is_null($result)) {
        //     throw new Exception("Rate not found");
        // }
        return $result;
    }
    public function prepareDataForMileageClaim($mileage_list)
    {

        $datatable_rows = array();
        foreach ($mileage_list as $key => $each_list) {
            $each_row["id"]              = $each_list->id;
            $each_row["transaction_date"] = isset($each_list->date)?$each_list->date->toFormattedDateString():"--";
            $last_name=($each_list->created_user->last_name)?' '.$each_list->created_user->last_name:'';
            $employee_no=isset($each_list->created_user->trashedEmployee->employee_no)
            ? ' ('.$each_list->created_user->trashedEmployee->employee_no.')' : '';
            $each_row["name"]         = $each_list->created_user->first_name.$last_name.$employee_no;
            $each_row["starting_location"]    = $each_list->starting_location;
            $each_row["destination"]    = $each_list->destination;
            $each_row["total_km"]    = $each_list->total_km;
            $each_row["amount"]    = '$'.($each_list->amount ?? '0');

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
        if (\Auth::user()->can('view_all_mileage_claim')) {
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
}

<?php

namespace Modules\Timetracker\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Models\TimeSheetApprovalConfiguration;
use Modules\Timetracker\Models\TimeSheetApprovalRating;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\TimesheetApprovalRatingConfiguration;
use App\Repositories\MailQueueRepository;
use App\Repositories\PushNotificationRepository;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\User;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\EmployeeRatingPolicies;
use PhpParser\Node\Expr\Cast\Double;
use Modules\Timetracker\Models\TimesheetApprovalPayperiodRating;

class EmployeeShiftAprovalRatingRepository
{
    protected $model;
    protected $timeSheetApprovalConfiguration;
    protected $payPeriodRepository;
    public function __construct(
        MailQueueRepository $mailQueueRepository,
        PayPeriodRepository $payPeriodRepository,
        EmployeeShiftPayperiod $model,
        TimeSheetApprovalConfiguration $timeSheetApprovalConfiguration,
        TimeSheetApprovalRating $timeSheetApprovalRatingModel,
        TimesheetApprovalPayperiodRating $timesheetApprovalPayperiodRatingModel

    ) {
        $this->model = $model;
        $this->timeSheetApprovalRatingModel = $timeSheetApprovalRatingModel;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->timeSheetApprovalConfiguration = $timeSheetApprovalConfiguration;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->timesheetApprovalPayperiodRatingModel = $timesheetApprovalPayperiodRatingModel;
    }

    public function timesheetApprovalRatings($payPeriodId = null, $deadlineDateTime)
    {
        Log::channel('timeSheetApprovalRatingLog')->info("Entering Repository" . $deadlineDateTime);
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $configrationDetails = TimeSheetApprovalConfiguration::first();
        if($configrationDetails->is_previous_week_enabled == true)
        {
            $payPeriodArr = $this->payPeriodRepository->getPreviousWeek();
            $payPeriodId = $payPeriodArr['ppid'];
            $weekdata = $payPeriodArr['week'];
        }else{
            if (empty($payPeriodId)) {
                $payPeriodObject = $this->payPeriodRepository->getCurrentPayperiod();
                $payPeriodId = $payPeriodObject ? $payPeriodObject->id : null;
            }
            // $deadline =  Carbon::now()->format('Y-m-d H:i:s');
            $weekdata = $this->payPeriodRepository->getPayperiodWeekByDate(Carbon::now()->format('Y-m-d'));
        }

        $employeeShiftPayperiod = $this->model->with(['customer.timeSheetApproverDetails'])
            ->whereActive(true)
            ->where("payperiod_week", $weekdata)
            ->where("approved", 1)
            ->where("is_rated", 0)
            ->where('pay_period_id', $payPeriodId)
            ->get();
        // Log::channel('timeSheetApprovalRatingLog')->info("Employee shift payperiod varibale list ".$employeeShiftPayperiod);
        foreach ($employeeShiftPayperiod as $key => $each_list) {
            if ($each_list->customer->time_sheet_approver_id != null) {
                $approvedTime = Carbon::parse($each_list->approved_date);
                $timesheetApprovalRatingConfigurations = TimesheetApprovalRatingConfiguration::all();
                $diffHours = $deadlineDateTime->diffInHours($approvedTime, false);
                Log::channel('timeSheetApprovalRatingLog')->info("Diff in hours " . $diffHours);
                $timesheetApprovalPayperiodRatingCount = $this->timesheetApprovalPayperiodRatingModel
                ->where([
                    'user_id' => $each_list->customer->time_sheet_approver_id,
                    'payperiod_id' => $each_list->pay_period_id])
                ->count();
                //TimesheetApprovalPayperiodRating Table Details
                if($timesheetApprovalPayperiodRatingCount == null)
                {
                    $data = ['user_id' => $each_list->customer->time_sheet_approver_id,'payperiod_id' =>  $each_list->pay_period_id];
                    $timesheetApprovalPayperiodRatingDetail = $this->timesheetApprovalPayperiodRatingModel->create($data);
                }else{
                    $timesheetApprovalPayperiodRatingDetail = $this->timesheetApprovalPayperiodRatingModel
                ->where([
                    'user_id' => $each_list->customer->time_sheet_approver_id,
                    'payperiod_id' => $each_list->pay_period_id])
                ->first();
                }
                foreach ($timesheetApprovalRatingConfigurations as $key => $each_row) {
                    $rowDiffernce = (float)$each_row->difference;
                    if ($diffHours < $rowDiffernce) {
                        // Log::channel('timeSheetApprovalRatingLog')->info("This hour if : ".$each_row."diffHours = ".$diffHours."flag = ".$flag);
                        Log::channel('timeSheetApprovalRatingLog')->info("Employee ID " . $each_list->customer->time_sheet_approver_id . "Approver Id" . $each_list->approved_by);
                        $matchThese = array('employee_shift_payperiod_id'=>$each_list->id);
                        $result = TimeSheetApprovalRating::updateOrCreate(
                            $matchThese,
                            [
                                'employee_shift_payperiod_id' => $each_list->id,
                                'timesheet_approval_payperiod_rating_id' => $timesheetApprovalPayperiodRatingDetail->id,
                                'payperiod_id' => $each_list->pay_period_id,
                                'user_id' => $each_list->customer->time_sheet_approver_id,
                                'deadline_datetime' => $deadlineDateTime->format('Y-m-d H:i:s'),
                                'rating' => $each_row->rating,
                                'latest_approved_by' =>  $each_list->approved_by,
                                'approved_datetime' =>  $approvedTime->format('Y-m-d H:i:s'),
                                'is_rating_calculated' => 0,
                            ]
                        );
                        break;
                    }
                }
                if (isset($result) && !empty($result)) {
                    //Payperiod Average Calculation
                    $timesheetApprovalRatingArray = TimeSheetApprovalRating::where(['payperiod_id'=>$each_list->pay_period_id,'user_id'=>$each_list->customer->time_sheet_approver_id])->get()->pluck('rating')->toArray();
                    $timesheetApprovalRatingAverageByPayperiod = array_sum($timesheetApprovalRatingArray)/count($timesheetApprovalRatingArray);
                    $this->timesheetApprovalPayperiodRatingModel->where('id', $timesheetApprovalPayperiodRatingDetail->id)->update(['rating' => $timesheetApprovalRatingAverageByPayperiod]);
                    $this->model->where('id',$each_list->id)->update(['is_rated' => 1]);
                }
            }
        }
            //unapproved employee shift details

            $pendingShiftPayperiod = $this->model->with(['customer.timeSheetApproverDetails'])
            ->whereActive(true)
            ->where("payperiod_week", $weekdata)
            ->where("approved", 0)
            ->where("is_rated", null)
            ->where('pay_period_id', $payPeriodId)
            ->get();

            if(!empty($pendingShiftPayperiod)){
                foreach ($pendingShiftPayperiod as $key => $each_pending_list) {
                    if ($each_pending_list->customer->time_sheet_approver_id != null) {
                        $timesheetApprovalPayperiodRatingCount = $this->timesheetApprovalPayperiodRatingModel
                        ->where([
                            'user_id' => $each_pending_list->customer->time_sheet_approver_id,
                            'payperiod_id' => $each_pending_list->pay_period_id])
                        ->count();
                        //TimesheetApprovalPayperiodRating Table Details
                        if($timesheetApprovalPayperiodRatingCount == null)
                        {
                            $data = ['user_id' => $each_pending_list->customer->time_sheet_approver_id,'payperiod_id' =>  $each_pending_list->pay_period_id];
                            $timesheetApprovalPayperiodRatingDetail = $this->timesheetApprovalPayperiodRatingModel->create($data);
                        }else{
                            $timesheetApprovalPayperiodRatingDetail = $this->timesheetApprovalPayperiodRatingModel
                        ->where([
                            'user_id' => $each_pending_list->customer->time_sheet_approver_id,
                            'payperiod_id' => $each_pending_list->pay_period_id])
                        ->first();
                        }
                        $ratingAfterDeadlinetimesheetApproval = TimesheetApprovalRatingConfiguration::latest('id')->first();
                        $matchThese = array('employee_shift_payperiod_id'=>$each_pending_list->id,'payperiod_id'=>$each_pending_list->pay_period_id);
                        $result = TimeSheetApprovalRating::updateOrCreate(
                            $matchThese,
                            [
                                'employee_shift_payperiod_id' => $each_pending_list->id,
                                'timesheet_approval_payperiod_rating_id' => $timesheetApprovalPayperiodRatingDetail->id,
                                'payperiod_id' => $each_pending_list->pay_period_id,
                                'user_id' => $each_pending_list->customer->time_sheet_approver_id,
                                'deadline_datetime' => $deadlineDateTime->format('Y-m-d H:i:s'),
                                'rating' => $ratingAfterDeadlinetimesheetApproval->rating,
                                'latest_approved_by' =>  $each_pending_list->approved_by,
                                'approved_datetime' =>  $each_pending_list->approved_date,
                                'is_rating_calculated' => 0,
                            ]
                        );
                        if (isset($result) && !empty($result)) {
                            $timesheetApprovalRatingArray = TimeSheetApprovalRating::where(['payperiod_id'=>$each_pending_list->pay_period_id,'user_id'=>$each_pending_list->customer->time_sheet_approver_id])->get()->pluck('rating')->toArray();
                            $timesheetApprovalRatingAverageByPayperiod = array_sum($timesheetApprovalRatingArray)/count($timesheetApprovalRatingArray);
                            $this->timesheetApprovalPayperiodRatingModel->where('id', $timesheetApprovalPayperiodRatingDetail->id)->update(['rating' => $timesheetApprovalRatingAverageByPayperiod]);
                            $this->model->where('id',$each_pending_list->id)->update(['is_rated' => 1]);
                        }
                    }
                }
            }
        if (isset($result) && !empty($result)) {
            return $this->addCustomerApproverRatingCalculation();
        }
    }

    public function addCustomerApproverRatingCalculation()
    {
        try{
            $approvalRatings = TimeSheetApprovalRating::where('is_rating_calculated', "!=", 1)->get();
            if ($approvalRatings->isEmpty()) {
                Log::channel('timeSheetApprovalRatingLog')->info("------Approval rating is empty-----");
                return false;
            } else {
                Log::channel('timeSheetApprovalRatingLog')->info("------Approval rating is not empty----");
                $rows = array();

                foreach ($approvalRatings as $ratings) {
                    $approvalRatingsById = TimeSheetApprovalRating::with(['users'])
                        ->where('user_id', $ratings->user_id)
                        ->where('is_rating_calculated', "!=", 1)
                        ->get();
                    if (!empty($approvalRatingsById)) {
                        foreach ($approvalRatingsById as $key => $value) {
                            $sum_arr[] = $value['rating'];
                            TimeSheetApprovalRating::where('id', $value->id)->update(['is_rating_calculated' => 1]);
                        }
                        $currentEmployeeRating =  Employee::where('user_id', $ratings->user_id)->first();
                        $totalScore =  TimeSheetApprovalRating::where('user_id', $ratings->user_id)->get()->pluck('rating')->toArray();
                        $avg =  array_sum($totalScore) / count($totalScore);
                        $currentAverage = $avg;
                        $result =  Employee::where('user_id', $ratings->user_id)
                            ->update(['time_sheet_approval_rating' => $currentAverage]);
                        $each_row['id'] = $ratings->id;
                        $each_row['user_id'] = $ratings->user_id;
                        $each_row['deadline_datetime'] = $ratings->deadline_datetime;
                        $each_row['approved_datetime'] = $ratings->approved_datetime;
                        array_push($rows, $each_row);
                    }
                }
                $collection = collect($rows);
                $timesheetApprovalUserIds = $collection->groupBy('user_id');
                 Log::channel('timeSheetApprovalRatingLog')->info("------Employee collection".$timesheetApprovalUserIds);
                return $this->sendAlertNotifications($timesheetApprovalUserIds);
            }
        }catch (\Exception $e) {
            Log::channel('timeSheetApprovalRatingLog')->info("------addCustomerApproverRatingCalculation Error".$e->getMessage());
        }

    }

    public function sendAlertNotifications($timesheetApprovalUserIds)
    {
        Log::channel('timeSheetApprovalRatingLog')->info("------Entering sendAletNotifications".$timesheetApprovalUserIds);
        try{
            foreach ($timesheetApprovalUserIds as $key => $value) {
                $user_Ids = array();
                $ratings = $value[0];
                $push_notification = new PushNotificationRepository();
                $timeSheetApproverDetails = Employee::where('user_id', $ratings['user_id'])->first();
                $rating = EmployeeRatingLookup::where('score', round($timeSheetApproverDetails->time_sheet_approval_rating))
                    ->first();
                $diffHours = Carbon::parse($ratings['deadline_datetime'])->diffInHours($ratings['approved_datetime'], false);
                $currentdiffHours = ($diffHours < 0) ? abs($diffHours) : $diffHours;
                $title = 'Timesheet Approval Rating';
                $subject = 'A rating of "' . $rating->score . ' - ' . $rating->rating . '" has been
                logged in your performance record.';
                // $user_Ids = array();
                $user_Ids[] = $timeSheetApproverDetails->user_id;
                $timeSheetApprovalRatingId = $ratings['id'];
                Log::channel('timeSheetApprovalRatingLog')->info($user_Ids);
                Log::channel('timeSheetApprovalRatingLog')->info($timeSheetApprovalRatingId);
                Log::channel('timeSheetApprovalRatingLog')->info($title);
                Log::channel('timeSheetApprovalRatingLog')->info($subject);
                Log::channel('timeSheetApprovalRatingLog')->info(PUSH_EMPLOYEE_RATING);
                // Log::channel('timeSheetApprovalRatingLog')->info("user Details".$user_Ids);
                $pushNotificationResult = $push_notification->sendPushNotification(
                    $user_Ids,
                    $timeSheetApprovalRatingId,
                    PUSH_EMPLOYEE_RATING,
                    $title,
                    $subject
                );
                if ($pushNotificationResult) {
                    Log::channel('timeSheetApprovalRatingLog')->info("Push Notification Response".$pushNotificationResult);
                    Log::channel('timeSheetApprovalRatingLog')->info("Notification Send Sucessfully");
                    $toGetUserEmail = User::where('id', $timeSheetApproverDetails->user_id)->first();
                    $to = $toGetUserEmail->email;
                    $model_name = 'Timesheet Approval Rating';
                    $this->mailQueueRepository->storeMail($to, $title, $subject, $model_name);
                    // return true;
                } else {
                    Log::channel('timeSheetApprovalRatingLog')->info("Notification could not send");
                    throw new \Exception("Notification could not send and not send");
                }
            }

        }catch (\Exception $e) {
            Log::channel('timeSheetApprovalRatingLog')->info("------sendAletNotifications Error".$e->getMessage());
        }
        return true;
    }



    public function timesheetApprovalReminder($template, $dayBefore)
    {
        Log::channel('timeSheetApprovalRatingLog')->info("Entering Repository");
        $configrationDetails = TimeSheetApprovalConfiguration::first();
        $deadlineDateTime = Carbon::parse($dayBefore.' '.$configrationDetails->time);
        Log::channel('timeSheetApprovalRatingLog')->info([$template, $deadlineDateTime]);
        if($configrationDetails->is_previous_week_enabled == true)
        {
            $payPeriodArr = $this->payPeriodRepository->getPreviousWeek();
            $payPeriodId = $payPeriodArr['ppid'];
            $weekdata = $payPeriodArr['week'];
        }else{
            if (empty($payPeriodId)) {
                $payPeriodObject = $this->payPeriodRepository->getCurrentPayperiod();
                $payPeriodId = $payPeriodObject ? $payPeriodObject->id : null;
            }
            $payperiodDate =  Carbon::now()->format('Y-m-d');
            $weekdata = $this->payPeriodRepository->getPayperiodWeekByDate($payperiodDate);
        }
        $employeeShiftPayperiod = $this->model->whereActive(true)
            ->with(['payperiod', 'customer'])
            ->where("payperiod_week", $weekdata)
            ->where("approved", 0)
            ->where('pay_period_id', $payPeriodId)
            ->get();
        $customerIds = [];
        foreach ($employeeShiftPayperiod as $key => $each_list) {
            if ($each_list->customer->time_sheet_approver_id != null) {
                $toGetUserEmail = User::where('id', $each_list->customer->time_sheet_approver_id)->with(['employee'])->first();
                Log::channel('timeSheetApprovalRatingLog')->info("Approver : ".$each_list->customer->time_sheet_approver_id);
                $helper_variables = array(
                    '{deadlineDate}' =>  $deadlineDateTime->format('Y-m-d'),
                    '{deadlineTime}' =>  $deadlineDateTime->format('H:i:s'),
                    '{payperiodDetails}' =>'Payperiod : ' .$each_list->payperiod->pay_period_name.
                    ' W'.$each_list->payperiod_week.' ('.$each_list->payperiod->start_date.
                    ' - '.$each_list->payperiod->end_date.')',
                    '{employeeName}' =>  $toGetUserEmail->getFullNameAttribute(),
                    '{employeeNumber}' =>  $toGetUserEmail->employee->employee_no,
                    '{clientDetails}' =>  $each_list->customer->getCustomerNameAndNumberAttribute(),
                );
                Log::channel('timeSheetApprovalRatingLog')->info("Template".$template." Csutomer Id".$each_list->customer_id);
                Log::channel('timeSheetApprovalRatingLog')->info("Helper Varibales".collect($helper_variables));
                if (!isset($customerIds[$each_list->customer->time_sheet_approver_id])) {
                    $customerIds[$each_list->customer->time_sheet_approver_id] = array();
                    $this->mailQueueRepository->prepareMailTemplate(
                        $template,
                        $each_list->customer_id,
                        $helper_variables,
                        'Modules\Timetracker\Models\EmployeeShiftPayperiod',
                        null,
                        $each_list->customer->time_sheet_approver_id
                    );
                }
            }
        }
    }

    public function employeeTimesheetApprovalRatingList($userId,$payPeriodId = null)
    {
        $payperiodIds = array_unique(TimeSheetApprovalRating::where('user_id', $userId)->get()->pluck('payperiod_id')->toArray());
        $payPeriodDetails = PayPeriod::whereIn('id',$payperiodIds)->orderBy('id','desc')->get();
        return $this->prepareDataForTimeSheetApprovalRatingList($payPeriodDetails,$userId);

    }

    public function prepareDataForTimeSheetApprovalRatingList($payPeriodDetails,$userId)
    {
        $datatable_rows = array();
        foreach ($payPeriodDetails as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : null;
            $each_row["pay_period_name"] = isset($each_list->pay_period_name) ? $each_list->pay_period_name : null;
            $each_row["start_date"] = isset($each_list->start_date) ? $each_list->start_date : null;
            $each_row["end_date"] = isset($each_list->end_date) ? $each_list->end_date : null;
            $timesheetApprovalRatingPayperiod = $this->timesheetApprovalPayperiodRatingModel
            ->where(['user_id' => $userId,'payperiod_id' => $each_list->id])->first();
            $timesheetApprovalRatingPayperiodAverage = isset($timesheetApprovalRatingPayperiod->rating) ? $timesheetApprovalRatingPayperiod->rating : null;
            $employeeRatingLookup = EmployeeRatingLookup::where('score',round($timesheetApprovalRatingPayperiodAverage))->first();
            $each_row["average_rating"] = isset($employeeRatingLookup->score) ? $employeeRatingLookup->score . '-' . $employeeRatingLookup->rating : null;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function timesheetApprovalByPayperiod($payPeriodId,$userId){
        $getTimeSheetApprovalRatingAll = TimeSheetApprovalRating::where('payperiod_id',$payPeriodId)
        ->where('user_id',$userId)
        ->with(['shiftPayperiod','shiftPayperiod.user','shiftPayperiod.user.employee','shiftPayperiod.customer'])
        ->orderBy('approved_datetime', 'desc')
        ->get();

        return $this->prepareDataForGetTimeSheetApprovalRatingAllList($getTimeSheetApprovalRatingAll);
    }

    public function prepareDataForGetTimeSheetApprovalRatingAllList($getTimeSheetApprovalRatingAll)
    {
        $datatable_rows = array();
        foreach ($getTimeSheetApprovalRatingAll as $key => $each_list) {
            $each_row["id"] = $each_list->id;
            $each_row["employee_name"] = $each_list->shiftPayperiod->user->getFullNameAttribute();
            $each_row["employee_no"] = $each_list->shiftPayperiod->user->employee->employee_no;
            $each_row["customer_name"] = $each_list->shiftPayperiod->customer->client_name;
            $each_row["customer_no"] = $each_list->shiftPayperiod->customer->project_number;
            $each_row["payperiod_week"] = $each_list->shiftPayperiod->payperiod_week;
            $each_row["deadline_date"] = Carbon::parse($each_list->deadline_datetime)->format('F d, Y');
            $each_row["deadline_time"] = Carbon::parse($each_list->deadline_datetime)->format('h:i A');
            $each_row["timesheet_submission_date"] = (isset($each_list->approved_datetime)) ? Carbon::parse($each_list->approved_datetime)->format('F d, Y') : '--';
            $each_row["timesheet_submission_time"] = (isset($each_list->approved_datetime)) ? Carbon::parse($each_list->approved_datetime)->format('h:i A') : '--';
            $deadline =  Carbon::parse($each_list->deadline_datetime);
            $each_row["variance"] = $deadline->diffInHours($each_list->approved_datetime, false);
            if (!empty($each_list->rating)) {
                $val = round($each_list->rating);
                $ratingDetails = EmployeeRatingLookup::where('score', $val)->first();
                $each_row["rating"] = $ratingDetails->score . '-' . $ratingDetails->rating;
            } else {
                $each_row["rating"] = null;
            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function getTimeSheetApprovalRating($userId)
    {
        $timeSheetApprovalRating = TimeSheetApprovalRating::where('user_id', $userId)
            ->with(['timesheetApprovalPayPeriodRating','shiftPayperiod','shiftPayperiod.customer', 'employeeRating', 'shiftPayperiod.payperiod', 'users','users.employee'])
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();
        $timeSheetApprovalRow = array();
        foreach ($timeSheetApprovalRating as $key => $value) {
            $object = new \stdClass();
            $object->id = $value->timesheet_approval_payperiod_rating_id;
            $object->date_time = $value->created_at;
            $object->manager_name = "System Generated";
            $object->manager_employee_id = $value->users->employee->employee_no;
            $currentRating =  round($value->users->employee->time_sheet_approval_rating);
            $ratingDetails = EmployeeRatingLookup::where('score', $currentRating)->first();
            $object->rating_text = ($ratingDetails != null) ? $ratingDetails->rating : '--';
            $deadline =  Carbon::parse($value->deadline_datetime);
            $object->subject = "Timesheet Approval Rating";
            $PolicyDeatils = EmployeeRatingPolicies::where('id',41)->first();
            $object->policy_description = (isset($PolicyDeatils->description) ? $PolicyDeatils->description : '--');
            $object->policy_name = (isset($PolicyDeatils->policy) ? $PolicyDeatils->policy : '--');
            $object->supporting_facts = (isset($value->supporting_facts)) ? $value->supporting_facts : '--';
            $object->timesheetApprovalRatingResponse = true;
            $object->timesheetApprovalResponded = isset($value->timesheetApprovalPayPeriodRating->response) ?  true : false;
            $timeSheetApprovalRow[] = $object;
        }
        return $timeSheetApprovalRow;
    }
    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */

    public function timeSheetApprovalRatingDelete($id)
    {
        $timeSheetApprovalRating = TimeSheetApprovalRating::where('id', $id)->first();
        if($timeSheetApprovalRating){
            $isRatingDeleted = TimeSheetApprovalRating::destroy($id);
        }
        if($isRatingDeleted){
            $totalScore = TimeSheetApprovalRating::where('user_id', $timeSheetApprovalRating->user_id)->get()->pluck('rating')->toArray();
            $avg =  array_sum($totalScore) / count($totalScore);
            $currentAverage = $avg;
            Employee::where('user_id', $timeSheetApprovalRating->user_id)->update(['time_sheet_approval_rating' => $currentAverage]);
            $timesheetApprovalPayperiod = $this->timesheetApprovalPayperiodRatingModel->where('id', $timeSheetApprovalRating->employee_shift_payperiod_id)->first();
            if(!empty($timesheetApprovalPayperiod)){
                $timesheetApprovalRatingArray = TimeSheetApprovalRating::where(['payperiod_id'=>$timeSheetApprovalRating->pay_period_id,'user_id'=>$timeSheetApprovalRating->user_id])->get()->pluck('rating')->toArray();
                $timesheetApprovalRatingAverageByPayperiod = array_sum($timesheetApprovalRatingArray)/count($timesheetApprovalRatingArray);
                $this->timesheetApprovalPayperiodRatingModel->where('id', $timeSheetApprovalRating->employee_shift_payperiod_id)->update(['rating' => $timesheetApprovalRatingAverageByPayperiod]);
            }
            return true;
        }
    }

    public function storeTimesheetApprovalRatingResponse($request)
    {
        return $this->timesheetApprovalPayperiodRatingModel->where('id', $request->id)->update(['response' => $request->response]);
    }
}

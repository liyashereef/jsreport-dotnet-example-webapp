<?php

namespace Modules\Hranalytics\Repositories;

use App\Repositories\MailQueueRepository;
use App\Repositories\PushNotificationRepository;
use Carbon\Carbon;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\User;
use Modules\Hranalytics\Models\UserRating;
use Modules\Supervisorpanel\Models\CustomerReportAdhoc;
use Modules\Timetracker\Repositories\EmployeeShiftAprovalRatingRepository;
class EmployeeMapRepository
{
    protected $mailQueueRepository;
    public function __construct(MailQueueRepository $mailQueueRepository, EmployeeShiftAprovalRatingRepository $employeeShiftAprovalRatingRepository)
    {
        $this->mailQueueRepository = $mailQueueRepository;
        $this->employeeShiftAprovalRatingRepository = $employeeShiftAprovalRatingRepository;
    }

    /**
     * Build an array to display in Map
     *
     * @param object $allocated_employees
     * @return array
     */
    public function prepareDataForEmployeeMap($allocated_employees)
    {

        $base_class = class_basename($allocated_employees->first());
        if ($base_class == 'EmployeeAllocation') {
            $employee_list = data_get($allocated_employees, "*.user");
        } else {
            $employee_list = $allocated_employees;
        }
        $list_data = array();

        foreach ($employee_list as $key => $data) {
            if ($data->active) {
                $value['rating'] = $data->employee->employee_rating;
                $value['employee_id'] = $data->id;
                $value['employee_no'] = $data->employee->employee_no;
                $value['first_name'] = $data->first_name;
                $value['last_name'] = $data->last_name;
                $value['full_name'] = $data->first_name . ' ' . $data->last_name;
                $value['address'] = $data->employee->employee_address;
                $value['city'] = $data->employee->employee_city;
                $value['postal_code'] = $data->employee->employee_postal_code;
                $value['phone_number'] = $data->employee->phone;
                $value['phone_ext'] = $data->employee->phone_ext;
                $value['work_email'] = $data->employee->employee_work_email;
                $value['latitude'] = $data->employee->geo_location_lat;
                $value['longitude'] = $data->employee->geo_location_long;
                $value['date_of_birth'] = $data->employee->employee_dob;
                $value['veteran_status'] = $data->employee->employee_vet_status;
                $value['current_wage'] = $data->employee->current_project_wage;
                $value['position'] = isset($data->employee->employeePosition) ? $data->employee->employeePosition->position : '--';
                $value["security_clearance"] = !($data->securityClearanceUser)->isEmpty() ? $data->securityClearanceUser->pluck('securityClearanceLookups.security_clearance')->toArray() : '--';
                $value["clearance_expiry"] = !($data->securityClearanceUser)->isEmpty() ? $data->securityClearanceUser->pluck('valid_until')->toArray() : '--';
                $value["project_number"] = isset($data->allocation->last()->customer) ? $data->allocation->last()->customer->project_number : '--';
                $value["project_name"] = isset($data->allocation->last()->customer) ? $data->allocation->last()->customer->client_name : '--';
                $value["start_date"] = $data->employee->employee_doj;
                $today = date('Y-m-d');
                $value["length_of_service"] = isset($data->employee->employee_doj) ? $this->dateDifference($today, $value["start_date"]) : '--';
                $value["age"] = isset($data->employee->employee_dob) ? $this->dateDifference($today, $value["date_of_birth"]) : '--';
                $value['image'] = $data->employee->image;
                array_push($list_data, $value);
            }
        }
        usort($list_data, function ($a, $b) {
            return strnatcasecmp($a['full_name'], $b['full_name']);
        });
        return $list_data;
    }
    /**
     * Function to calculate date difference in years
     *
     * @param $id
     * @return value
     */
    public function dateDifference($date_1, $date_2, $differenceFormat = '%y')
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat);
    }
    /**
     * Function to calculate average rating
     *
     * @param $id
     * @return value
     */
    public function averageRating($id)
    {
        $rating = \DB::table('user_ratings')
            ->select(\DB::raw('avg(rating) as average'))
            ->where('employee_id', '=', $id)->get()
        ;
        return $rating->first()->average;
    }

    /**
     * Function to Store rating
     *
     * @param request
     * @return response
     */
    public function storeEmployeeRating($request)
    {
        $userRating = EmployeeRatingLookup::where('id', '=', $request->get('employee_rating_lookup_id'))->first();
        $data['user_id'] = $request->get('user_id');
        $data['employee_id'] = $request->get('employee_id');
        $data['customer_id'] = $request->get('customer_id');
        $data['subject'] = $request->get('subject');
        $data['employee_rating_lookup_id'] = $request->get('employee_rating_lookup_id');
        $data['supporting_facts'] = $request->get('supporting_facts');
        $data['notify_employee'] = ($request->get('notify_employee') == 'on') ? 1 : 0;
        $data['policy_id'] = $request->get('policy_id');
        $data['rating'] = $userRating->score;
        $performance = UserRating::updateOrCreate(array('id' => $request->get('id')), $data);
        $rating = $this->averageRating($request->get('employee_id'));
        $employee_rating = Employee::where('user_id', $request->get('employee_id'))->update(['employee_rating' => $rating]);
        if (($data['notify_employee'] == 1) && ($performance->id != null)) {
            $push_notification = new PushNotificationRepository();
            $fromUser = User::where('id', $request->get('user_id'))->first();
            $title = 'New Rating';
            $subject = 'You have received a new rating from ' . $fromUser->getFullNameAttribute();
            $user_Ids = array();
            $user_Ids[] = $request->get('employee_id');
            $push_notification->sendPushNotification($user_Ids, $performance->id, PUSH_EMPLOYEE_RATING, $title, $subject);
            $toGetUserEmail = User::where('id', $request->get('employee_id'))->first();
            $to = $toGetUserEmail->email;
            $model_name = 'UserRatings';
            $mail_queue = $this->mailQueueRepository->storeMail($to, $title, $subject, $model_name);
        }
        return ['success' => $performance];
    }

    /**
     * Function to filter
     *
     * @param request,$allocated_employees
     * @return response
     */

    public function getFilter($request, $allocated_employees)
    {
        if ((clone $allocated_employees->get())->isEmpty()) {
            return $allocated_employees->get();
        } else {
            $base_class = class_basename((clone $allocated_employees)->first());
            //Get user.employee relation inside employee alocation
            if ($base_class == 'EmployeeAllocation') {
                $employee_relation = 'user.employee';
            } else {
                $employee_relation = 'employee';
            }

            $employee_list = $allocated_employees->wherehas($employee_relation, function ($query) use ($request) {
                if (null != $request && $request->get('veteran_status') != null) {
                    $query->where(\DB::raw("
                    CASE
                        WHEN employee_vet_status IS NULL THEN 0
                        ELSE employee_vet_status
                    END
                    "), '=', $request->get('veteran_status'));
                }
                if ($request->get('wage_low') != null) {
                    $query->where('current_project_wage', '>=', $request->get('wage_low'));
                }
                if ($request->get('wage_high') != null) {
                    $query->where('current_project_wage', '<=', $request->get('wage_high'));
                }
                if ($request->get('age_low') != null) {
                    $query->where(\DB::raw("TIMESTAMPDIFF(YEAR, employee_dob, CURDATE())"), '>=', $request->get('age_low'));
                }
                if ($request->get('age_high') != null) {
                    $query->where(\DB::raw("TIMESTAMPDIFF(YEAR, employee_dob, CURDATE())"), '<=', $request->get('age_high'));
                }
                if ($request->get('length_low') != null) {
                    $query->where(\DB::raw("
                    CASE
                        WHEN employee_doj IS NULL THEN 0
                        ELSE TIMESTAMPDIFF(YEAR, employee_doj, CURDATE())
                    END
                    "), '>=', $request->get('length_low'));
                }
                if ($request->get('length_high') != null) {
                    $query->where(\DB::raw("
                    CASE
                        WHEN employee_doj IS NULL THEN 0
                        ELSE TIMESTAMPDIFF(YEAR, employee_doj, CURDATE())
                    END
                    "), '<=', $request->get('length_high'));
                }
                if ($request->get('rating_low') != null) {
                    $query->where(\DB::raw("
                    CASE
                        WHEN employee_rating IS NULL THEN 0.0000
                        ELSE employee_rating
                    END
                    "), '>=', number_format($request->get('rating_low'), 4));
                }
                if ($request->get('rating_high') != null) {
                    $query->where(\DB::raw("
                    CASE
                        WHEN employee_rating IS NULL THEN 0.0000
                        ELSE employee_rating
                    END
                    "), '<=', number_format($request->get('rating_high'), 4));
                }
                if ($request->get('position') != null) {
                    $query->where('position_id', '=', $request->get('position'));
                }
                if ($request->get('clearance') != null) {
                    $query->whereHas('user.securityClearanceUser', function ($query) use ($request) {
                        $query->where('security_clearance_lookup_id', '=', $request->get('clearance'));
                    });
                }
            });
            return $employee_list->get();
        }
    }
    /**
     * Preparing array with datatable values
     *
     * @param $id
     * @return array
     */
    public function listEmployeeTimeoff($id)
    {
        $employee_leave = CustomerReportAdhoc::with(
            'leave_reason',
            'payperiod',
            'customer_payperiod_template',
            'customer_payperiod_template.customer',
            'customer_payperiod_template.customer.employeeLatestCustomerSupervisor'
        )->where('employee_id', $id)->get();
        $list_data = array();
        foreach ($employee_leave as $key => $data) {
            $value['date'] = $data->date;
            $value['employee_id'] = $data->employee_id;
            $value['hours_off'] = $data->hours_off;
            $value['reason'] = $data->leave_reason->reason;
            $value['notes'] = $data->notes;
            $value['payperiod'] = isset($data->payperiod) ? $data->payperiod->pay_period_name : '--';
            $value['project_number'] = isset($data->customer_payperiod_template->customer->client_name) ? ($data->customer_payperiod_template->customer->project_number . '/' . $data->customer_payperiod_template->customer->client_name) : '--';
            $value['supervisor'] = isset($data->customer_payperiod_template->customer->employeeLatestCustomerSupervisor) ? ($data->customer_payperiod_template->customer->employeeLatestCustomerSupervisor->supervisor->first_name . '/' . $data->customer_payperiod_template->customer->employeeLatestCustomerSupervisor->supervisor->employee->employee_no) : '--';
            array_push($list_data, $value);
        }
        return $list_data;
    }

    /**
     * Get employee ratings for App
     *
     * @param $userid
     */
    public function getEmployeeRating($userid)
    {
        $employee_rating = UserRating::with('user', 'userRating', 'policyDetails', 'customer')
            ->where('notify_employee', 1)
            ->where('employee_id', $userid)
            ->orderBy('created_at', 'desc')->take(10)->get();
        $employee_ratings = [];
        $employee_rating_mergged_array = [];
        foreach ($employee_rating as $key => $value) {
            $object = new \stdClass();
            $object->id = $value->id;
            $object->date_time = $value->created_at;
            $object->manager_name = $value->user->full_name;
            $object->manager_employee_id = $value->user->trashedEmployee->employee_no;
            $object->rating_text = $value->userRating->rating;
            $object->policy_description = (isset($value->policyDetails) ? $value->policyDetails->description : '--');
            $object->policy_name = (isset($value->policyDetails) ? $value->policyDetails->policy : '--');
            $object->supporting_facts = $value->supporting_facts;
            $object->subject = $value->subject;

            if (isset($value->customer)) {
                if ($value->customer->employee_rating_response == 1) {
                    if ($value->response == '') {
                        $created = new Carbon($value->created_at);
                        $now = Carbon::now();
                        $date_diff = $created->diffInDays($now);
                        if ($value->customer->employee_rating_response >= $date_diff) {
                            $response = 1;
                        } else {
                            $response = 0;
                        }
                    } else {
                        $response = 2;
                    }
                } else {
                    $response = 0;
                }
            } else {
                $response = 0;
            }

            $object->response = $response;
            $employee_ratings[] = $object;
        }
        $timesheetApprovalRatings = $this->employeeShiftAprovalRatingRepository->getTimeSheetApprovalRating($userid);
        $employee_rating_mergged_array =  array_merge($employee_ratings, $timesheetApprovalRatings);
        return $employee_rating_mergged_array;
    }

    /**
     * Get employee ratings by Supervisor for App
     *
     * @param $userid
     */
    public function getEmployeeRatingBySupervisor($userid)
    {
        $user_rating = UserRating::with('userRating', 'policyDetails', 'employee')->where('user_id', $userid)->orderBy('created_at', 'desc')->take(10)->get();
        $user_ratings = [];
        foreach ($user_rating as $key => $value) {
            if (isset($value->employee)) {
                $object = new \stdClass();
                $object->id = $value->id;
                $object->date_time = $value->created_at;
                $object->employee_name = $value->employee->full_name;
                $object->employee_id = $value->employee->trashedEmployee->employee_no;
                $object->rating_text = $value->userRating->rating;
                $object->policy_description = (isset($value->policyDetails) ? $value->policyDetails->description : '--');
                $object->policy_name = (isset($value->policyDetails) ? $value->policyDetails->policy : '--');
                $object->supporting_facts = $value->supporting_facts;
                $object->subject = $value->subject;
                if ($value->response != '') {
                    $response = 2;
                } else {
                    $response = 1;
                }
                $object->response = $response;
                $user_ratings[] = $object;
            }
        }
        return $user_ratings;
    }

    /**
     * Store emplyee rating respnse for App
     *
     */
    public function storeRatingResponse($request)
    {
        return UserRating::where('id', $request->id)->update(['response' => $request->response]);
    }
}

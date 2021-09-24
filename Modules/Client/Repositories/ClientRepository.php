<?php

namespace Modules\Client\Repositories;

use App\Services\HelperService;
use Auth;
use Mail;
use Modules\Admin\Models\CustomerTemplateEmail;
use Modules\Admin\Models\CustomerTemplateUseridMapping;
use Modules\Admin\Models\EmailTemplate;
use Modules\Admin\Repositories\ClientFeedbackLookupRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Client\Mail\NewClientConcern;
use Modules\Client\Models\ClientEmployeeFeedback;
use App\Repositories\MailQueueRepository;

//use Modules\Admin\Models\ClientRepository;

class ClientRepository
{
    public function __construct(
        CustomerEmployeeAllocationRepository $customerEmployeeAllocation,
        CustomerRepository $customerReporsitory,
        HelperService $helperService,
        ClientFeedbackLookupRepository $clientFeedbackLookupRepository,
        MailQueueRepository $mailQueueRepository
    ) {
        $this->customerEmployeeAllocation = $customerEmployeeAllocation;
        $this->customerReporsitory = $customerReporsitory;
        $this->model = new ClientEmployeeFeedback;
        $this->helperService = $helperService;
        $this->clientFeedbackLookupRepository = $clientFeedbackLookupRepository;
        $this->mailQueueRepository=$mailQueueRepository;
    }
    /**
     * Get employees assigned to the client
     */
    public function getEmployees($allocated_customers_arr = null, $role = null, $role_except = false)
    {
        if (!isset($allocated_customers_arr)) {
            //get allocted customers
            $allocated_customers_arr = $this->customerEmployeeAllocation->getAllocatedCustomers(Auth::user());
        }
        //get users allocated to the project
        $allocated_customers_user_arr = $this->customerEmployeeAllocation->allocationList($allocated_customers_arr, $role, $role_except);

        //get the userlist except for current user
        $alloc_list = ($allocated_customers_user_arr->forget(Auth::user()->id));
        $emp_arr = array();
        foreach ($alloc_list as $key => $emp_list) {
            $emp_arr[$emp_list->id] = $emp_list->full_name . ' (' . $emp_list->employee->employee_no . ')';
        }
        return ($emp_arr);
    }

    /**
     * Function to get projects assigned to login user
     */
    public function getProjects()
    {
        //get allocted customers
        $allocated_customers_arr = $this->customerEmployeeAllocation->getAllocatedCustomers(Auth::user());
        $customer_details_arr = $this->customerReporsitory->getCustomers($allocated_customers_arr);
        $customers_arr = array();
        foreach ($customer_details_arr as $key => $customers) {
            $customers_arr[$customers->id] = $customers->client_name . ' (' . $customers->project_number . ')';
        }
        return $customers_arr;
    }

    /**
     * Get all resource list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * Function to get projects assigned to login user
     */
    public function getFeedbacks()
    {
        //get allocted customers
        $allocated_customers_arr = $this->clientFeedbackLookupRepository->getList();
        return $allocated_customers_arr;
    }
    /**
     * Save client feedback
     * @param user_client_id integer User id
     * @param request Request
     */
    public function store($user_client_id, $request)
    {
        // security check

        $project_not_accessible = !array_key_exists($request->customer_id, $this->getProjects());
        $user_not_accessible = !(array_key_exists($request->employee_id, $this->getEmployees([$request->customer_id])));
        $feedback_not_accessible = !(array_key_exists($request->feedback_id, $this->getFeedbacks()));
        // dd($project_not_accessible, $user_not_accessible, $feedback_not_accessible, $this->getFeedbacks(), $request->feedback_id, $this->getProjects()->toArray());
        // if ($project_not_accessible || $feedback_not_accessible && $user_not_accessible) {
        //     return response()->json($this->helperService->returnFalseResponse());
        // }
        //prepare data array
        $data = array(
            'user_id' => $request->employee_id,
            'customer_id' => $request->customer_id,
            'feedback_id' => $request->feedback_id,
            'status_lookup_id' => (isset($request->status_lookup_id)) ? $request->status_lookup_id : null,
            'reg_manager_notes' => (isset($request->reg_manager_notes)) ? $request->reg_manager_notes : null,
        );

        if (!isset($request->id)) {
            $data['created_by'] = $user_client_id;
            $data['employee_rating_lookup_id'] = $request->employee_rating_lookup_id;
            $data['client_feedback'] = $request->customer_feedback;
        } else {
            $data['updated_by'] = $user_client_id;
        }
        // saving the transaction
        try {
            \DB::beginTransaction();
            $client_emp_rating_data = $this->model->updateOrCreate(array('id' => $request->id), $data);
            // $email_template = EmailTemplate::where('type_id', config('globals.client_feedback_type'))->first();
            // $customer_template_id = CustomerTemplateEmail::where('customer_id', $request->customer_id)->where('template_id', config('globals.client_feedback_type'))->first();
            // if ($customer_template_id != null && $email_template != null) {
            //     $user_ids = CustomerTemplateUseridMapping::with('userDetails')->where('template_email_id', $customer_template_id->id)->get();
            //     $full_name = data_get($user_ids, '*.userDetails.full_name');
            //     $email = data_get($user_ids, '*.userDetails.email');
            //     $email_ids = array_combine($full_name, $email);
            //     $customer_details = $this->customerReporsitory->getSingleCustomer($request->customer_id);
            //     $client_employee_feedback = $this->model->where('id', $client_emp_rating_data['id'])->with('customer', 'createdUser.employee', 'userRating', 'clientFeedbacks')->first();
            
            //     $searchReplaceArray = array(
            //         '{projectNumber}' => $customer_details['project_number'],
            //         '{client}' => $customer_details['client_name'],
            //         '{rating}' => $client_employee_feedback->userRating->rating,
            //         '{loggedInUser}' => $client_employee_feedback->createdUser->full_name,
            //         '{loggedInUserEmployeeNumber}' => $client_employee_feedback->createdUser->employee->employee_no);
            //     foreach ($email_ids as $name => $to) {
            //         $mail_content = $this->replaceText($name, $email_template, $searchReplaceArray);
            //         $this->sendNotification($mail_content['subject'], $mail_content['body'], $to, 'mail.concern-created');
            //     }
            // }
             $customer_details = $this->customerReporsitory->getSingleCustomer($request->customer_id);
             $client_employee_feedback = $this->model->where('id', $client_emp_rating_data['id'])->with('customer', 'createdUser.employee', 'userRating', 'clientFeedbacks')->first();
              $helper_variables = array(
                    '{projectNumber}' => $customer_details['project_number'],
                    '{client}' => $customer_details['client_name'],
                    '{rating}' => $client_employee_feedback->userRating->rating,
                    '{loggedInUser}' => $client_employee_feedback->createdUser->full_name,
                    '{loggedInUserEmployeeNumber}' => $client_employee_feedback->createdUser->employee->employee_no);
            $emailResult = $this->mailQueueRepository->prepareMailTemplate('client_feedback', $customer_details['id'], $helper_variables, null);
            \DB::commit();
            return ['success' => $client_emp_rating_data];
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function replaceText($name, $email_template, $searchReplaceArray)
    {
        $searchReplaceArray['{receiverFullName}'] = $name;
        $mail['subject'] = str_replace(
            array_keys($searchReplaceArray),
            array_values($searchReplaceArray),
            $email_template->email_subject
        );
        $mail['body'] = str_replace(
            array_keys($searchReplaceArray),
            array_values($searchReplaceArray),
            htmlspecialchars_decode($email_template->email_body)
        );
        return $mail;
    }

    /**
     * function to prepare and return data for table
     *
     */
    public function getTableList($customerId = [], $returnPreparedData = true)
    {
        $rating_list = $this->model->with(['user', 'userRating', 'user.roles', 'createdUser', 'customer', 'clientFeedbacks']);
        if (!(\Auth::User()->can('view_all_client_feedback'))) {
            $rating_list = $rating_list->where('created_by', \Auth::User()->id);
        }
        if (!empty($customerId)) {
            $rating_list = $rating_list->whereIn('customer_id', $customerId);
        }
        $rating_list = $rating_list->orderBy('created_at', 'desc')->get();
        if ($returnPreparedData) {
            return $this->prepareTableList($rating_list);
        } else {
            return $rating_list;
        }
    }

    /**
     * Function to prepare rows for table
     * @param rating_list ClientEmployeeFeedback
     */
    public function prepareTableList($rating_list)
    {
        $datatable_rows = array();
        foreach ($rating_list as $key => $each_employee) {
            $each_row['id'] = data_get($each_employee, "id");
            $each_row['employee_id'] = data_get($each_employee, "user_id");
            $each_row['customer_id'] = data_get($each_employee, "customer_id");
            $each_row['full_name'] = null != (data_get($each_employee, "user.full_name")) ? data_get($each_employee, "user.full_name") : '--';
            $each_row['role'] = null != HelperService::snakeToTitleCase(data_get($each_employee, "user.roles.0.name")) ? HelperService::snakeToTitleCase(data_get($each_employee, "user.roles.0.name")) : '--';
            $each_row['rating'] = data_get($each_employee, "userRating.rating");
            $each_row['comments'] = data_get($each_employee, "client_feedback");
            $each_row['project'] = data_get($each_employee, "customer.client_name");
            $each_row['date_time'] = data_get($each_employee, "created_at")->toDateTimeString();
            $each_row['rated_by'] = data_get($each_employee, "createdUser.full_name");
            $each_row['feedback'] = data_get($each_employee, "clientFeedbacks.feedback");
            $each_row["status_lookup_id"] = isset($each_employee->whistleblowerStatusLookup) ? $each_employee->whistleblowerStatusLookup->name : null;
            $each_row['reg_manager_notes'] = data_get($each_employee, "reg_manager_notes");
            $each_row["status_color_code"] = isset($each_employee->whistleblowerStatusLookup) ? $each_employee->whistleblowerStatusLookup->status : null;

            array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }

    public function getSingle($id)
    {
        return $this->model->with('clientFeedbacks', 'user')->find($id);
    }

/**
 * To send mail notification
 *
 * @param [type] $job
 * @param [type] $to
 * @param [type] $cc
 * @param [type] $template
 * @return void
 */
    public function sendNotification($subject, $concern, $to, $template, $cc = null)
    {
        $mail = Mail::to($to);
        if ($cc != null) {
            $mail->cc($cc);
        }

        $mail->queue(new NewClientConcern($subject, $concern, $template));
    }
}

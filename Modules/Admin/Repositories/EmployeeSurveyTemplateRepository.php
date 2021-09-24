<?php

namespace Modules\Admin\Repositories;

use Modules\Hranalytics\Models\EmployeeSurveyTemplate;
use Modules\Hranalytics\Models\EmployeeSurveyQuestion;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use App\Repositories\PushNotificationRepository;

class EmployeeSurveyTemplateRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ClientFeedbackLookup instance.
     *
     * @param  \App\Models\ClientFeedbackLookup $clientFeedbackLookup
     */
    public function __construct(
        EmployeeSurveyTemplate $employeeSurveyTemplate,
        EmployeeSurveyQuestion $employeeSurveyQuestion,
        UserRepository $userRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {
        $this->model = $employeeSurveyTemplate;
        $this->employeeSurveyQuestion=$employeeSurveyQuestion;
        $this->userRepository = $userRepository;
        $this->customerEmployeeAllocationRepository=$customerEmployeeAllocationRepository;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'survey_name', 'customer_based', 'start_date', 'expiry_date','active','created_at', 'updated_at'])->get();
    }

    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('survey_name', 'asc')->pluck('survey_name', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $questions= $this->employeeSurveyQuestion->where('survey_id', $id)->delete();
        return $this->model->destroy($id);
    }

    public function triggerPushNotification($customer_arr = null, $role_arr = null, $template_id)
    {
        $users= $employeesAllocated=array();
        if ($role_arr!=null) {
            $users=$this->userRepository->getUserLookup($role_arr);
            $user_id_arr=array_keys($users);
        }
        if ($customer_arr!=null) {
            $employeesAllocated=$this->customerEmployeeAllocationRepository->allocationList($customer_arr)->pluck('id')->toArray();
        }
        if ($role_arr==null && $customer_arr==null) {
             $user_id_arr=$this->userRepository->getUserTableList(true)->pluck('id')->toArray();
        }
        $combined_user_arr=array_merge($user_id_arr, $employeesAllocated);
        $push_notification = new PushNotificationRepository();
        $title = 'New Employee Survey';
        $subject = 'You have received a new survey ';
        $push_notification->sendPushNotification($combined_user_arr, $template_id, PUSH_EMPLOYEE_SURVEY, $title, $subject);
        return true;
    }
}

<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\EmailTemplate;
use Modules\Admin\Models\TrainingCourse;
use Modules\Admin\Models\CustomerTemplateEmail;
use Modules\Admin\Models\CustomerTemplateUseridMapping;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Models\User;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\EmailNotificationHelper;
use Modules\Admin\Models\EmailTemplateRoles;

class ClientEmailTemplateRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $emailTemplate, $customerTemplateEmail, $customerTemplateUseridMapping, $userRepository, $customerEmployeeAllocationRepository, $user, $emailNotificationHelper;

    /**
     * Create a new Repository instance.
     *
     * @param  \App\Models\TrainingCategory $trainingCategory

     */
    public function __construct(EmailTemplateRoles $emailTemplateRoles, EmailTemplate $emailTemplate, CustomerTemplateEmail $customerTemplateEmail, CustomerTemplateUseridMapping $customerTemplateUseridMapping, UserRepository $userRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository, User $user, EmailNotificationHelper $emailNotificationHelper)
    {
        $this->model = $emailTemplate;
        $this->customerTemplateEmail = $customerTemplateEmail;
        $this->customerTemplateUseridMapping = $customerTemplateUseridMapping;
        $this->userRepository = $userRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->user = $user;
        $this->emailNotificationHelper = $emailNotificationHelper;
        $this->emailTemplateRoles = $emailTemplateRoles;
    }

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->where('type_id', $id)->get()->first();
    }

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {

        //$data['email_body']=htmlspecialchars( $data['email_body']);
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {

        return $this->model->destroy($id);
    }

    /**
     * Save alloation list
     *
     * @param  $id
     * @return object
     */
    public function saveAllocation($request)
    {
        try {
            \DB::beginTransaction();
            $checked_values = [];
            $userOfTemplate = $this->deleteUserMappingRecords($request);
            $roleOfTemplate = $this->deleteRoleMappingRecords($request);
            $role_based = isset($request['role_based']) ? 1 :0;
            $customer = ($request['show_customer_block'] == 0) ? 0 : $request['customer'];
            //Logic for saving each customer's allocation when "selected all customers" choosen for type
            if ($request['customer'] == null && $request['show_customer_block'] == 1) {
                $customers = Customer::pluck('id')->toArray();
                if ($request['areamanagers'] != null) {
                    $checked_values[] = 'area_manager';
                }
                if ($request['supervisors'] != null) {
                    $checked_values[] = 'supervisor';
                }
                $users = $request['user'] != null ? $this->userRepository->getAllByUserIds($request['user'], null, null, null, null, true)->toArray() : null;
                $roles = $request['role'];
                foreach ($customers as $each_customer) {
                    $template_id = $this->customerTemplateEmail->updateOrCreate(array('template_id' => $request['type'], 'customer_id' => $each_customer), ['role_based' => $role_based,'send_to_areamanagers' => $request['areamanagers'], 'send_to_supervisors' => $request['supervisors']])->id;
                    // $allocated_list = (!empty($checked_values)) ? $this->customerEmployeeAllocationRepository->allocationList($each_customer, $checked_values, false, true)->pluck('id')->toArray() : [];
                    // if (!empty($allocated_list)) {
                    //     foreach ($allocated_list as $key => $user) {
                    //         $this->customerTemplateUseridMapping->create(['template_email_id' => $template_id, 'user_id' => $user]);
                    //     }
                    // }
                    if (!empty($roles)) {
                        foreach ($roles as $key => $each_roles) {
                            $this->emailTemplateRoles->updateOrCreate(['email_template_id' => $template_id, 'role_id' => $each_roles]);
                        }
                    }
                    //Extra added users(other than checkbox checked) save
                    if (!empty($users)) {
                        foreach ($users as $key => $each_user) {
                            $this->customerTemplateUseridMapping->create(['template_email_id' => $template_id, 'user_id' => $each_user['id']]);
                        }
                    }
                }
            }
            $template_id = $this->customerTemplateEmail->updateOrCreate(array('id' => $request['id']), ['customer_id' => $customer, 'template_id' => $request->type, 'role_based' => $role_based, 'send_to_areamanagers' => $request['areamanagers'], 'send_to_supervisors' => $request['supervisors']])->id;
            if (!empty($request['user'])) {
                foreach ($request['user'] as $key => $user) {
                    $this->customerTemplateUseridMapping->create(['template_email_id' => $template_id, 'user_id' => $user]);
                }
            }
            if (!empty($request['role'])) {
                foreach ($request['role'] as $key => $role) {
                    $this->emailTemplateRoles->updateOrCreate(['email_template_id' => $template_id, 'role_id' => $role]);
                }
            }
            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function deleteUserMappingRecords($request)
    {
        $userOfTemplate = $this->customerTemplateEmail->with('usersIdMapping')->where('template_id', $request['type'])->when($request['customer'] != 0, function ($query) use ($request) {
                    $query->where('customer_id', $request['customer']);
        })->get();
        foreach ($userOfTemplate as $key => $each_relation) {
            $each_relation->usersIdMapping()->delete();
            // $each_relation->delete();
        }
    }

    public function deleteRoleMappingRecords($request)
    {
        $roleOfTemplate = $this->customerTemplateEmail->with('roleIdMapping')->where('template_id', $request['type'])->when($request['customer'] != 0, function ($query) use ($request) {
                    $query->where('customer_id', $request['customer']);
        })->get();
        foreach ($roleOfTemplate as $key => $each_relation) {
            $each_relation->roleIdMapping()->delete();
        }
    }

    /**
     * Edit alloation list
     *
     * @param  $id
     * @return object
     */
    public function editAllocation($template_id, $customer_id = null)
    {
        $area_manager = $this->customerEmployeeAllocationRepository->allocationList($customer_id, ['area_manager'], false, true)->pluck('id')->toArray();
        $supervisor = $this->customerEmployeeAllocationRepository->allocationList($customer_id, ['supervisor'], false, true)->pluck('id')->toArray();
        $userlsit = $this->customerTemplateEmail->with('usersIdMapping.userDetails')->where('customer_id', $customer_id)->where('template_id', $template_id)->get();
        $role_list = $this->customerTemplateEmail->with('roleIdMapping')->where('customer_id', $customer_id)->where('template_id', $template_id)->get();
        $result = (array_combine(data_get($userlsit, '*.usersIdMapping.*.userDetails.id'), data_get($userlsit, '*.usersIdMapping.*.userDetails.full_name')));
        return (['result' => $result, 'data' => $userlsit->first(), 'area_manager' => $area_manager, 'supervisor' => $supervisor, 'role_list' => $role_list]);
    }

    /**
     * Get alloation list
     *
     * @param  $id
     * @return object
     */
    public function getAllocationList($request)
    {
        $all = ['supervisor', 'area_manager'];
        $selected = empty($request['userid']) ? [] : $request['userid'];
        $unselected = array_diff($all, $selected);
        $customer = $request['customer'] == 0 ? null : $request['customer'];
        if (empty($selected)) {
            $full_list = ($request['customer'] == 0) ? $this->customerEmployeeAllocationRepository->allocationList($customer, $unselected, false, true)->pluck('full_name', 'id')->toArray() : [];
        } else {
            $full_list = $this->customerEmployeeAllocationRepository->allocationList($customer, $selected, false, true)->pluck('full_name', 'id')->toArray();
        }
        $allocated_list_remove = (empty($unselected)) ? [] : $this->customerEmployeeAllocationRepository->allocationList($customer, $unselected, false, true)->pluck('full_name', 'id')->toArray();
        return (['allocated_list_remove' => $allocated_list_remove, 'full_data' => $full_list]);
    }

    /**
     * Get alloation list
     *
     * @param  $id
     * @return object
     */
    public function allocationList($type_id = null, $customer_id = null, $role = null, $role_except = false, $customer_based = null)
    {
        $data = $this->customerTemplateEmail->
        select(
            [
                'template_id','customer_id','send_to_areamanagers','send_to_supervisors','role_based','customer_template_emails.id'
            ]
        )->with('usersIdMapping.userDetails', 'customer', 'type')->whereNotNull('customer_id')->when(($type_id !== null && $type_id !== "null"), function ($query) use ($type_id) {
            $query->where('template_id', $type_id);
        })->when($customer_based, function ($query) {
            $query->whereHas('customer');
        })
         ->when(is_null($customer_based), function ($query) {
             $query->whereHas('type', function ($query) {
                 $query->where('customer_based', 0);
             });
              $query->orWhereHas('customer');
         });


        if ($customer_id != null) {
            if (is_array($customer_id)) {
                $data->whereIn('customer_id', $customer_id);
            } else {
                $data->where('customer_id', $customer_id);
            }
        }
        
        return $data;
        
        // $datatable_rows = array();
        // foreach ($data as $key => $each_record) {
        //     $each_row=array();
        //     $each_row["client_name"] = isset($each_record->customer) ? $each_record->customer->client_name : null;
        //     $each_row["id"] = $each_record->id;
        //     $each_row["type"] =$each_record->type->type;
        //     $each_row["display_name"] = $each_record->type->display_name;
        //     $each_row["customer_based"] = $each_record->type->customer_based;
        //     $users = data_get($each_record, 'usersIdMapping.*');
        //     $each_row["client_id"] = isset($each_record->customer) ? $each_record->customer->id : null;
        //     $each_row["type_id"] = $each_record->template_id;
        //     $arr_users['user_name'] = [];
        //     foreach ($users as $key => $each_user_id) {
        //         if (isset($each_user_id->userDetails) && $each_user_id->userDetails->active) {
        //             array_push($arr_users['user_name'], $each_user_id->userDetails->full_name);
        //         }
        //     }
        //     if ($each_record->send_to_areamanagers==1) {
        //         $areamanagerlist=$this->customerEmployeeAllocationRepository->allocationList($each_record->customer_id, ['area_manager'], false, true)->pluck('full_name')->toArray();
        //         $arr_users['user_name']= array_merge($arr_users['user_name'], $areamanagerlist);
        //     }
        //     if ($each_record->send_to_supervisors==1) {
        //         $supervisorlist=$this->customerEmployeeAllocationRepository->allocationList($each_record->customer_id, ['supervisor'], false, true)->pluck('full_name')->toArray();
        //         $arr_users['user_name']=array_merge($arr_users['user_name'], $supervisorlist);
        //     }
        //     $latest_users_data = $arr_users;
        //     $combined_result = $each_row + $latest_users_data;
        //     array_push($datatable_rows, $combined_result);
        // }
       

       // return $datatable_rows;
    }

    public function getHelperList($id, $includeSelectedDefaultHelpersOnly = [])
    {
        $qry = $this->emailNotificationHelper->where('email_notification_type_id', $id);
        if (empty($includeSelectedDefaultHelpersOnly)) {
            $qry->orWhere('email_notification_type_id', null);
        } else {
            $qry->orWhereIn('replace_string', $includeSelectedDefaultHelpersOnly);
        }
        $result = $qry->pluck('replace_value', 'replace_string');
        return $result;
    }
}

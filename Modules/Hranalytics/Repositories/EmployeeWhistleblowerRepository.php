<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Hranalytics\Models\EmployeeWhistleblower;
use Modules\Admin\Models\WhistleblowerStatusLookup;
use Modules\Admin\Models\User;
use Auth;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Models\EmployeeWhistleblowerLogs;



class EmployeeWhistleblowerRepository
{
    protected $employeeWhistleblowerModel, $userModel, $cusempallocationRepository, $userRepository;

    /***
     *  Constructor function
     *
     */
    public function __construct(
        EmployeeWhistleblower $employeeWhistleblowerModel,
        User $userModel,
        CustomerEmployeeAllocationRepository $cusemployeeallocationRepository,
        EmployeeAllocationRepository $employeeAllocationrepository,
        UserRepository $userRepository,
        HelperService $helperService
    ) {
        $this->employeeWhistleblowerModel = $employeeWhistleblowerModel;
        $this->usermodel = $userModel;
        $this->customeremployeeallocationrepository = $cusemployeeallocationRepository;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->userrepository = $userRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Function to get all the employee whistleblower records
     *
     *  @param empty
     *  @return  array
     *
     */
    public function getEmployeeWhistleblowersList()
    {

        //$employee_whistleblowers_list = $this->employeeWhistleblowerModel->with(['user.trashedEmployee','whsitleblowerCategories','whsitleblowerPriorites'])->get();
        $employee_whistleblowers_list = $this->employeeWhistleblowerModel->with(['whsitleblowerCategories', 'whsitleblowerPriorites', 'createdby.trashedEmployee', 'policy'])->get();
        return $this->prepareDataForWhistleblowers($employee_whistleblowers_list);
    }

    /**
     *  Function to get all the user and allocated employees whistleblower records
     *
     *  @param empty
     *  @return array
     *
     */
    public function getAllocattedEmployeeWhistleblowerList()
    {

        $supervisor_id = \Auth::user()->id;
        $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned($supervisor_id);
        $employees->prepend($supervisor_id);
        //$employee_whistleblowers_list = $this->employeeWhistleblowerModel->whereIn('employee_id',$employees)->orWhere('created_by',$supervisor_id)->with(['user.trashedEmployee','whsitleblowerCategories','whsitleblowerPriorites'])->get();
        $employee_whistleblowers_list = $this->employeeWhistleblowerModel->whereIn('created_by', $employees)->orWhere('created_by', $supervisor_id)->with(['whsitleblowerCategories', 'whsitleblowerPriorites', 'createdby.trashedEmployee', 'policy'])->get();
        return $this->prepareDataForWhistleblowers($employee_whistleblowers_list);
    }
    /**
     *  Function to get employee created whistleblower records
     *
     *  @param empty
     *  @return array
     *
     */
    public function getCreatedEmployeeWhistleblowerList()
    {

        $supervisor_id = \Auth::user()->id;
        // $employee_whistleblowers_list = $this->employeeWhistleblowerModel->where('employee_id',$supervisor_id)->orWhere('created_by',$supervisor_id)->with(['user.trashedEmployee','whsitleblowerCategories','whsitleblowerPriorites'])->get();
        $employee_whistleblowers_list = $this->employeeWhistleblowerModel->where('created_by', $supervisor_id)->with(['whsitleblowerCategories', 'whsitleblowerPriorites', 'createdby.trashedEmployee', 'policy'])->get();
        return $this->prepareDataForWhistleblowers($employee_whistleblowers_list);
    }

    /**
     *  Function to format the employee whistleblower records for datatable listing
     *
     *  @param array whistleblower records
     *  @return array formatted whistleblower records
     *
     */
    public function prepareDataForWhistleblowers($employee_whistleblowers_list)
    {

        $datatable_rows = array();
        foreach ($employee_whistleblowers_list as $key => $each_list) {
            $each_row["id"]              = isset($each_list->id) ? $each_list->id : null;
            $each_row["date"]            = date('F d, Y', strtotime($each_list->created_at));
            //$each_row["employee_details"] = data_get($each_list,'user.name_with_emp_no');
            $each_row["created_by"]      = data_get($each_list, 'createdby.name_with_emp_no');
            $each_row["customer"]        = isset($each_list->customer) ? $each_list->customer->getClientNameAndNumberAttribute() : "--";
            $each_row["subject"]         = isset($each_list->whistleblower_subject) ? $each_list->whistleblower_subject : null;
            $each_row["category"]        = isset($each_list->whsitleblowerCategories->roles) ? $each_list->whsitleblowerCategories->roles : null;
            $each_row["policy"]          = isset($each_list->policy->policy) ? $each_list->policy->policy : null;
            $each_row["priority"]        = isset($each_list->whsitleblowerPriorites->priority) ? $each_list->whsitleblowerPriorites->priority : null;
            $each_row["note"]            = isset($each_list->whistleblower_documentation) ? $each_list->whistleblower_documentation : null;
            $each_row["latitude"]        = isset($each_list->geo_location_lat) ? $each_list->geo_location_lat : null;
            $each_row["longitude"]       = isset($each_list->geo_location_long) ? $each_list->geo_location_long : null;
            $each_row["status"]          = isset($each_list->whistleblowerStatusLookup) ? $each_list->whistleblowerStatusLookup->name : null;
            $each_row["reg_manager_notes"]   = isset($each_list->reg_manager_notes) ? $each_list->reg_manager_notes : null;
            if (isset($each_list->whistleblowerStatusLookup)) {
                if ($each_list->whistleblowerStatusLookup->status == 1) {
                    $each_row["status_color"] = "Open";
                } else if ($each_list->whistleblowerStatusLookup->status == 2) {
                    $each_row["status_color"] = "In Progress";
                } else if ($each_list->whistleblowerStatusLookup->status == 3) {
                    $each_row["status_color"] = "Closed";
                }
            }else{
                $each_row["status_color"] = "None";
            }

            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     *  Function to store employee whistleblower entry
     *
     *  @param array
     *  @return json
     *
     */
    public function store($request)
    {

        //prepare data array
        $data = array(
            // 'employee_id' => $request->employee_id,
            'customer_id' => $request->customer_id,
            'whistleblower_subject' => $request->whistleblower_subject,
            'whistleblower_category_id' => $request->whistleblower_category_id,
            'whistleblower_priority_id' => $request->whistleblower_priority_id,
            'whistleblower_documentation' => $request->whistleblower_documentation,
            'policy_id' => $request->policy_id,
            'status' => (isset($request->status)) ? $request->status : null,
            'reg_manager_notes' => (isset($request->reg_manager_notes)) ? $request->reg_manager_notes : null,
            // 'created_by' => Auth::id(),
        );

        // saving the transaction
        try {
            \DB::beginTransaction();
            $client_emp_rating_data = $this->employeeWhistleblowerModel->updateOrCreate(array('id' => $request->id), $data);
            if(isset($request->status)){
                EmployeeWhistleblowerLogs::create([
                    "whistle_blower_id" => $client_emp_rating_data->id,
                    "status_id" => $client_emp_rating_data->status,
                    "created_by" => \Auth::user()->id
                ]);
            }
            \DB::commit();
            return ['success' => $client_emp_rating_data];
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /***
     *  Get single record employee whistleblower
     *
     *  @param integer id - whistleblower id
     *  @return array $result
     *
     */
    public function getSingle($id)
    {
        $result = $this->employeeWhistleblowerModel->with('createdby.trashedEmployee')->find($id);
        $result['created_at_date'] =  date('d/m/Y', strtotime($result->created_at));
        $result['created_by'] = $result->createdby->name_with_emp_no;
        return $result;
    }

    public function getAllEmployees($supervisor_id)
    {

        $employees = [];
        $employees =   $this->userrepository->getUserLookup(null, ['super_admin', 'admin'], true, false, null, false);
        $logged_in_employee = User::find($supervisor_id);
        $employees[$supervisor_id] = $logged_in_employee->name_with_emp_no;
        return $employees;
    }

    /***
     * Function to get allocated employees
     *
     * @param integer supervisor_id
     *
     * @return array
     *
     */

    public function getAllocatedEmployees($supervisor_id)
    {
        $employees = $this->employeeAllocationRepository->getEmployeeAssigned($supervisor_id);
        $employee_array = [];
        $logged_in_employee = User::find($supervisor_id);

        if ($employees) {
            foreach ($employees as $employee) {
                $name_with_emp_no = data_get($employee, 'user.name_with_emp_no');
                $employee_id =  data_get($employee, 'user.id');
                $employee_array[$employee_id] = $name_with_emp_no;
            }
        }

        $employee_array[$supervisor_id] = $logged_in_employee->name_with_emp_no;
        return $employee_array;
    }

    /***
     * Function to get created employees
     *
     * @param integer supervisor_id
     *
     * @return array
     *
     */

    public function getcreatedEmployees($supervisor_id)
    {

        $employee_array = [];
        $logged_in_employee = User::find($supervisor_id);
        $employee_array[$supervisor_id] = $logged_in_employee->name_with_emp_no;
        return $employee_array;
    }

    /**
     * to store data from employee whistleblower app
     */
    public function submitEmployeeWhistleblowerApp($request)
    {
        $lookupStatus = "";
        $openStatus = WhistleblowerStatusLookup::where("inital_status", 1)->first();
        if ($openStatus) {
            $lookupStatus = $openStatus->id;
        }
        $whistleblower = new EmployeeWhistleblower;
        $whistleblower->customer_id = $request->get('projectId');
        $whistleblower->status = $lookupStatus;
        $whistleblower->reg_manager_notes = $request->get('reg_manager_notes');
        $whistleblower->whistleblower_subject = $request->get('subject');
        $whistleblower->whistleblower_category_id = $request->get('categoryId');
        $whistleblower->whistleblower_priority_id = $request->get('priorityId');
        $whistleblower->policy_id = $request->get('policyId');
        $whistleblower->geo_location_lat = $request->get('latitude');
        $whistleblower->geo_location_long = $request->get('longitude');
        $whistleblower->whistleblower_documentation = $request->get('notes');
        $whistleblower->created_by = $request->get('user_id');
        $whistleblower->save();
        return $whistleblower;
    }
}

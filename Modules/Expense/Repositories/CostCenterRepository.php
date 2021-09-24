<?php

namespace Modules\Expense\Repositories;

use Modules\Admin\Models\User;
use Modules\Admin\Models\RegionLookup;
use Modules\Admin\Repositories\UserRepository;
use Modules\Expense\Models\ExpenseCostCenterLookup;



class CostCenterRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ExitTerminationReasonLookup instance.
     */
    public function __construct(
        ExpenseCostCenterLookup $model,
        User $userModel,
        UserRepository $userRepository,
        RegionLookup $regionModel
    ) {
        $this->model = $model;
        $this->usermodel = $userModel;
        $this->regionmodel = $regionModel;
        $this->userrepository = $userRepository;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $course_center_details =  $this->model->select([
            'id', 'center_number',
            'center_owner_id', 'center_senior_manager_id', 'region_id',
            'description', 'created_at', 'updated_at'
        ])->with(['centerOwners', 'seniorMangers', 'regions'])->get();
        return $this->prepareDataForCourseCenters($course_center_details);
    }

    /**
     *  Function to format the Document Name  record for datatable listing
     * 
     *  @param array document name records
     *  @return array formatted document name records
     * 
     */
    public function prepareDataForCourseCenters($course_center_details)
    {
        $datatable_rows = array();
        foreach ($course_center_details as $key => $each_list) {
            $each_row["id"]              = $each_list->id;
            $each_row["center_number"]   = $each_list->center_number;
            $each_row["center_owner"]   =  data_get($each_list, 'centerOwners.name_with_emp_no');
            $each_row["center_senior_manager"]   =  data_get($each_list, 'seniorMangers.name_with_emp_no');
            $each_row["region"] = $each_list->regions->region_name;
            $each_row["description"] = $each_list->description;
            array_push($datatable_rows, $each_row);
        }


        return $datatable_rows;
    }


    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getCategoryDetails($id)
    {
        return $this->categorymodel->orderBy('id')->where('document_type_id', $id)->get();
    }

    public function save($data)
    {

        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getLookups()
    {
        $default      = ['' => 'Please Select'];
        $employees =   $this->userrepository->getUserLookup(null,['admin','super_admin'], true, false, null, false);
        asort($employees);
        $regions = $this->regionmodel->orderBy('region_name', 'asc')
            ->pluck('region_name', 'id')->toArray();
        $lookups['user_lookups'][] = $default + $employees;
        $lookups['regions'][] = $default + $regions;;
        return $lookups;
    }
    public function getList()
    {
        return $this->model->orderBy('center_number', 'asc')->pluck('center_number', 'id')->toArray();
    }
}

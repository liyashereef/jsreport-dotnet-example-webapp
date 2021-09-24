<?php

namespace Modules\Admin\Repositories;

use Illuminate\Support\Facades\Auth;
use App\Services\HelperService;
use Modules\Admin\Models\TimeOffRequestTypeSetting;

class ExperienceWiseLeaveMasterRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct(TimeOffRequestTypeSetting $experienceWiseLeaveMaster, HelperService $helperService)
    {
        $this->model = $experienceWiseLeaveMaster;
        $this->helperService = $helperService;
    }



    public function getAll()
    {
        $data = $this->model->get();
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {
            $each_row["id"] = $each_record->id;
            $each_row["min_experience"] = $each_record->min_experience;
            $each_row["no_of_leaves"] = $each_record->no_of_leaves;
            $each_row["time_off_request_type_id"] = $each_record->timeoffRequestType->request_type;
            $each_row["active"] = $each_record->active;
            if ($each_record->active == 1) {
                $each_row["active"] = "Active";
            } else {
                $each_row["active"] = "In Active";
            }
            $each_row["created_at"] = $each_record->created_at;
            $each_row["updated_at"] = $each_record->updated_at;
            array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }

    /**
     * Display details of single Skill Name
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('timeoffRoles')->find($id);
    }

    /**
     * Store a newly created Skill in storage.
     *
     * @param  $data
     * @return object
     */
    /**
     * Remove the Skill from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}

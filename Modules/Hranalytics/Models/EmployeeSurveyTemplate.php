<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSurveyTemplate extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "survey_name", "customer_based", "role_based", "start_date",
        "expiry_date", "created_by", "active"
    ];

    /**
     * Relation to Answers
     *
     * @return type
     */
    public function templateEntries()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeSurveyEntry', 'survey_id', 'id');
    }

    /**
     * Relation to form
     *
     * @return type
     */
    public function templateForm()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeSurveyQuestion', 'survey_id', 'id');
    }
    /**
     * Relation to form
     *
     * @return type
     */
    public function customerAllocation()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeSurveyCustomerAllocation', 'survey_id', 'id');
    }
    /**
     * Relation to form
     *
     * @return type
     */
    public function roleAllocation()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeSurveyRoleAllocation', 'survey_id', 'id');
    }

    public function employeesurveycustomerallocation()
    {
        return $this->hasMany("Modules\Hranalytics\Models\EmployeeSurveyCustomerAllocation", "survey_id", "id");
    }

    public function employeesurveyroleallocation()
    {
        return $this->hasMany("Modules\Hranalytics\Models\EmployeeSurveyRoleAllocation", "survey_id", "id");
    }
}

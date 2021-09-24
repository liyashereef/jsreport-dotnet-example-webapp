<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSurveyCustomerAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ["customer_id", "survey_id", "created_by"];

    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\EmployeeSurveyTemplate', 'survey_id', 'id');
    }

    public function customer()
    {
        return $this->hasOne("Modules\Admin\Models\Customer", "id", "customer_id");
    }
}

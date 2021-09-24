<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSurveyRoleAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ["role_id","survey_id","created_by"];


    public function template()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\EmployeeSurveyTemplate', 'survey_id', 'id');
    }
}

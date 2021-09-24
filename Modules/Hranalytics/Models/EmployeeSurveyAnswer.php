<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSurveyAnswer extends Model
{
    use SoftDeletes;
    protected $fillable = ["entry_id", "survey_id", "question_id", "question", "answer_type", "answer", "created_by"];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function surveyQuestion()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\EmployeeSurveyQuestion', 'question_id', 'id');
    }
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function surveyCustomer()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeSurveyCustomerAllocation', 'survey_id', 'id');
    }
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function surveyEntry()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\EmployeeSurveyEntry', 'entry_id', 'id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function surveyRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'answer', 'id');
    }
}

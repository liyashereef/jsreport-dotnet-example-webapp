<?php

namespace Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTermination extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'candidate_id',
        'employee_exit_interview_id',
        'age',
        'education_1',
        'education_2',
        'education_3',
        'screening_questions_avg_count',
        'length_of_service',
        'no_of_guards',
        'position',
        'current_wage_1',
        'current_wage_2',
        'current_wage_3',
        'distance_between_work_and_home',
        'time_between_work_and_home'
    ];

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function employeeExitInterview()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\EmployeeExitInterview', 'employee_exit_interview_id', 'id');
    }
}

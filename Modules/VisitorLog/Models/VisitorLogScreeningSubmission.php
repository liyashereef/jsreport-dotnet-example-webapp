<?php

namespace Modules\VisitorLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogScreeningSubmission extends Model
{
    use SoftDeletes;
    protected $table = 'visitor_log_screening_submissions';
    protected $fillable = ['visitor_log_screening_template_id','customer_id','uid','passed','screened_at'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function VisitorLogScreeningTemplate(){
        return $this->belongsTo('Modules\Admin\Models\VisitorLogScreeningTemplate', 'visitor_log_screening_template_id', 'id')->withTrashed();
    }

    public function visitorLogScreeningSubmissionQuestionAnswersWithTrashed(){
        return $this->hasMany('Modules\Client\Models\VisitorLogScreeningSubmissionQuestionAnswers', 'visitor_log_screening_submission_id', 'id')->withTrashed();
    }

}


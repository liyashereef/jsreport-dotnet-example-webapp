<?php

namespace Modules\Client\Repositories;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Client\Models\VisitorLogScreeningSubmission;
use Modules\Client\Models\VisitorLogScreeningSubmissionQuestionAnswers;

class VisitorLogScreeningSubmissionRepository
{

    public function __construct()
    {
        $this->helper_service = new HelperService();
        $this->model = new VisitorLogScreeningSubmission();
        $this->submissionQuestionAnswers = new VisitorLogScreeningSubmissionQuestionAnswers();
    }

    /**
     * fetch visitor screening template data by uid for checking duplication.
     * @param id
     */
    public function getByUID($uid){
        return $this->model->where('uid',$uid)->get();
    }

     /**
     * Store visitor screening submission store.
     * @param array
     */
    public function save($inputs){
        return $this->model->create($inputs);
    }

    /**
     * Store visitor screening submission question and answers store.
     * @param array
     */
    public function saveQuestionAnswers($inputs){
        return $this->submissionQuestionAnswers->create($inputs);
    }

    public function getAllMyVisitorScreeningSubmissions($inputs){

        $result = $this->model->whereIn('customer_id',$inputs['customer_id'])
        ->when(isset($inputs['start_date']) && $inputs['start_date'] !=='null' && !empty($inputs['start_date']), function($query) use($inputs){
            return $query->whereDate('screened_at','>=',$inputs['start_date']);
        })
        ->when(isset($inputs['end_date']) && $inputs['end_date'] !=='null' && !empty($inputs['end_date']), function($query) use($inputs){
            return $query->whereDate('screened_at','<=',$inputs['end_date']);
        })
        ->when(isset($inputs['passed']) && $inputs['passed'] !=='null', function($query) use($inputs){
            return $query->where('passed',$inputs['passed']);
        })
        ->with(['customer'=>function($query){
            return $query->select('id','project_number','client_name');
        },
        'VisitorLogScreeningTemplate'=>function($query){
            return $query->select('id','name');
        },
        'visitorLogScreeningSubmissionQuestionAnswersWithTrashed'=>function($query){
            return $query->select('id','visitor_log_screening_submission_id',
            'visitor_log_screening_template_question_id','answer',
            'visitor_log_screening_template_question_str','visitor_log_screening_template_question_expected_answer');
        }
        ])
        ->orderBy('created_at', 'desc')
        ->get();
       return $result;
    }

    public function getAttemptedQuestionAndAnswers($submission_id){
        return $this->submissionQuestionAnswers->where('visitor_log_screening_submission_id',$submission_id)->get();
    }

    public function getVisitorScreeningPassedCount($inputs){
        return $this->model->whereIn('customer_id',$inputs['customer_id'])
        ->when(isset($inputs['start_date']) && $inputs['start_date'] !=='null' && !empty($inputs['start_date']), function($query) use($inputs){
            return $query->whereDate('created_at','>=',$inputs['start_date']);
        })
        ->when(isset($inputs['end_date']) && $inputs['end_date'] !=='null' && !empty($inputs['end_date']), function($query) use($inputs){
            return $query->whereDate('created_at','<=',$inputs['end_date']);
        })
        ->when(isset($inputs['passed']) && $inputs['passed'] !=='null', function($query) use($inputs){
            return $query->where('passed',$inputs['passed']);
        })
        ->count();
    }

}

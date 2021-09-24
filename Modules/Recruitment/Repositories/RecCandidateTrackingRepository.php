<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecCandidateTracking;
use Modules\Recruitment\Models\RecProcessSteps;
use Carbon\Carbon;
use Modules\Recruitment\Models\RecProcessTab;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Recruitment\Models\RecCandidateAwareness;
use Modules\Recruitment\Models\RecCandidateJobDetails;

class RecCandidateTrackingRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecCandidateTracking instance.
     *
     * @param  \App\Models\RecCandidateTracking $recCandidateTracking
     */
    public function __construct(RecCandidateTracking $recCandidateTracking, RecProcessSteps $recProcessSteps, RecProcessTab $recProcessTab)
    {
        $this->model = $recCandidateTracking;
        $this->recProcessSteps=$recProcessSteps;
        $this->recProcessTab=$recProcessTab;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id','candidate_id', 'completed_date','process_lookups_id', 'process_tab_id','notes','entered_by'])->get();
    }
    
    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('process_lookups_id', 'asc')->pluck('process_lookups_id', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function saveTracking($candidate_id, $step_name, $tabid = false, $job_id = null, $user_notes = null)
    {
            $loginAssigned= $this->recProcessSteps->where('step_name', $step_name)->first();
            $processTab= $this->recProcessTab->where('system_name', $step_name)->first();
            $tracking['candidate_id']=$candidate_id;
            $tracking['completed_date']=Carbon::now();
            $tracking['process_lookups_id']=$loginAssigned->id;
            $tracking['job_id']=$job_id;
        if (!empty($processTab)) {
            $tracking['process_tab_id']=$processTab->id;
        } elseif ($tabid!=false) {
            $tracking['process_tab_id']=$tabid;
        }
            $tracking['notes']=isset($user_notes)?$user_notes:$loginAssigned->notes;
            $tracking['entered_by']= \Auth::id();
            // $this->model->where('candidate_id', $candidate_id)->where('process_lookups_id', $loginAssigned->id)->delete();
            $data=$this->model->create($tracking);
        return $data;
    }
  /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteOldCandidateTracking($candidate_id, $step_name)
    {
        $step= $this->recProcessSteps->where('step_name', $step_name)->first();
        return $this->model->where('candidate_id', $candidate_id)->where('process_lookups_id', $step->id)->delete();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getProcessStep($candidate_id)
    {
          $response=[];
          $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $candidate_id)->first();
          $completed = $this->model->where('candidate_id', $candidate_id)->orderBy('process_lookups_id', 'desc')->first();
        if (!empty($completed)) {
            // latest completed step
            $current_step = $this->recProcessSteps->where('step_order', $completed->process_lookups_id)->first();
            $response['current_step'] = $current_step->step_name;
            $response['current_tab'] = $current_step->tab_id;
            $response['current_route'] = $current_step->route;
            $status=RecCandidate::select('is_completed')->where('id', $candidate_id)->first();
            if ($response['current_tab'] > 7 && !$job_details) {
                $response['next_step'] = 'apply_for_jobs';
                $response['next_tab'] = 7;
                $response['next_route'] = '/form/apply';
            } elseif (($response['current_step']=='competency') && ($status['is_completed'] == 0)) {
                $response['next_step'] = 'competency';
                $response['next_tab'] = 4;
                $response['next_route'] = '/form/competency';
            } else {
                $nextstep = $this->recProcessSteps->where('step_order', $completed->process_lookups_id + 1)->first();
                if (!empty($nextstep)) {
                    // Max tab -> next step to be done
                    $response['next_step'] = $nextstep->step_name;
                    $response['next_tab'] = $nextstep->tab_id;
                    $response['next_route'] = $nextstep->route;
                } elseif (empty($nextstep) && $response['current_step']=='onboarding_meeting_completed') {
                    $response['next_step'] = $current_step->step_name;
                    $response['next_tab'] = $current_step->tab_id;
                    $response['next_route'] = $current_step->route;
                }
            }
            return $response;
        } else {
            return false;
        }
    }

    public function deleteTrackingWhenRoleRejected($candidate_id)
    {
        $process_lookups_id_before_applyjob=[1,2,3,4,5,6,7,8,9];
        RecCandidateAwareness::where('candidate_id', $candidate_id)->update(array('interview_score' => null,'reference_score'=>null,'interview_date'=>null,'interview_notes'=>null,'reference_date'=>null,'reference_notes'=>null));
        return $this->model->where('candidate_id', $candidate_id)->whereNotIn('process_lookups_id', $process_lookups_id_before_applyjob)->delete();
    }

    public function checkRecordExists($candidate_id)
    {
        return $this->model->where('candidate_id', $candidate_id)->where('process_lookups_id', 20)->exists(); //Process lookup id 20 -uniform recieved
    }
}

<?php

namespace Modules\Contracts\Repositories;

use DB;
use Modules\Admin\Models\RfpProcessStepLookups;
use Modules\Admin\Models\User;
use Modules\Contracts\Models\RfpDetails;
use Modules\Contracts\Models\RfpDetailsWinLose;
use Modules\Contracts\Models\RfpEvaluationCriteria;
use Modules\Contracts\Models\RfpProjectExecutionDate;
use Modules\Contracts\Models\RfpResponseSubmissionDate;
use Modules\Contracts\Models\RfpTrackingStage;

class RfpRepository
{

    protected $model, $rfpModel, $rfpResponseSubmissionDate, $rfpProjectExecutionDate, $rfpEvaluationCriteria, $rfpTrackingStage, $rfpProcessstepLookups, $rfpDetailsWinLose;

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    public function __construct(
        RfpDetails $rfpModel,
        RfpResponseSubmissionDate $rfpResponseSubmissionDate,
        RfpProjectExecutionDate $rfpProjectExecutionDate,
        RfpEvaluationCriteria $rfpEvaluationCriteria,
        RfpTrackingStage $rfpTrackingStage,
        RfpProcessStepLookups $rfpProcessstepLookups,
        RfpDetailsWinLose $rfpDetailsWinLose
    ) {
        $this->rfpModel = $rfpModel;
        $this->rfpResponseSubmissionDate = $rfpResponseSubmissionDate;
        $this->rfpProjectExecutionDate = $rfpProjectExecutionDate;
        $this->rfpEvaluationCriteria = $rfpEvaluationCriteria;
        $this->rfpTrackingStage = $rfpTrackingStage;
        $this->rfpProcessstepLookups = $rfpProcessstepLookups;
        $this->rfpDetailsWinLose = $rfpDetailsWinLose;
        $this->model = new User();
    }
    public function getAll()
    {
        $rfpList = array();
        if (\Auth::user()->can('view_all_rfp')) {
            $rfpList = $this->rfpModel->select([
                'id', 'updated_at', 'employee_id', 'rfp_site_name', 'submission_deadline',
                'rfp_site_city', 'rpf_status','assign_resource_id'])
                ->selectRaw(\DB::raw('(select (select process_steps from rfp_process_steps where id=rfp_process_steps_id) from rfp_tracking_stages 
                where rfp_details_id=rfp_details.id 
                and (select deleted_at from rfp_process_steps where id=rfp_process_steps_id) IS NULL and deleted_at is null
                order by rfp_tracking_stages.rfp_process_steps_id desc limit 0,1) as laststepprocess'))
                ->selectRaw(\DB::raw('(select (select step_number from rfp_process_steps where id=rfp_process_steps_id) from rfp_tracking_stages 
                where rfp_details_id=rfp_details.id 
                and (select deleted_at from rfp_process_steps where id=rfp_process_steps_id) IS NULL and deleted_at is null
                order by rfp_tracking_stages.rfp_process_steps_id desc limit 0,1) as laststepprocessnumber'))
                ->with('lastTrack.tracking_process', 'user', 'clientOnboarding')->orderBy('id', 'desc')
                ->get();
        } else if (\Auth::user()->can('create_rfp')) {
            $rfpList = $this->rfpModel->select([
                'id', 'updated_at', 'employee_id', 'rfp_site_name', 'submission_deadline',
                'rfp_site_city', 'rpf_status','assign_resource_id'])
                ->selectRaw(\DB::raw('(select (select process_steps from rfp_process_steps where id=rfp_process_steps_id) from rfp_tracking_stages 
                where rfp_details_id=rfp_details.id 
                and (select deleted_at from rfp_process_steps where id=rfp_process_steps_id) IS NULL
                order by rfp_tracking_stages.rfp_process_steps_id desc limit 0,1) as laststepprocess'))
                ->selectRaw(\DB::raw('(select (select step_number from rfp_process_steps where id=rfp_process_steps_id) from rfp_tracking_stages 
                where rfp_details_id=rfp_details.id 
                and (select deleted_at from rfp_process_steps where id=rfp_process_steps_id) IS NULL
                order by rfp_tracking_stages.rfp_process_steps_id desc limit 0,1) as laststepprocessnumber'))
                ->with('lastTrack.tracking_process', 'user', 'clientOnboarding')->where('created_by', \Auth::User()->id)
                ->orderBy('id', 'desc')->get();
        }
       
        return $this->prepareDataForrfpList($rfpList);
    }

    /**
     *  Function to get all the client records
     *
     *  @param empty
     *  @return  array
     *
     */
    public function prepareDataForrfpList($rfpList)
    {
        $datatable_rows = array();
        foreach ($rfpList as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
            $each_row["updated_at"] = isset($each_list->updated_at) ? $each_list->updated_at->format('j M Y') : "--";
            $each_row["prepared_by"] = isset($each_list->user->first_name) ? $each_list->user->first_name . " " . $each_list->user->last_name : "--";
            $each_row["rfp_site_name"] = isset($each_list->rfp_site_name) ? $each_list->rfp_site_name : "--";
            $each_row["submission_deadline"] = isset($each_list->submission_deadline) ? date("j M Y", strtotime($each_list->submission_deadline)) : "--";
            $each_row["location"] = isset($each_list->rfp_site_city) ? $each_list->rfp_site_city : "--";
            $each_row["status"] = isset($each_list->rpf_status) ? $each_list->rpf_status : "--";
            $each_row["last_step"] = isset($each_list->laststepprocess) && $each_list->rpf_status!="Pending" ? $each_list->laststepprocess : "-";
            $each_row["step_number"] = isset($each_list->laststepprocessnumber) && $each_list->rpf_status!="Pending" ? $each_list->laststepprocessnumber : "";
            $each_row['rfp_win_lose'] = isset($each_list->lastStatusWinLose->status) ? $each_list->lastStatusWinLose->status : "";
            $each_row["rpf_status"] = isset($each_list->rpf_status) ? $each_list->rpf_status : "0";
            $each_row["assign_resource_id"] = isset($each_list->assign_resource_id) ? $each_list->assign_resource_id : "0";
            $each_row["client_onboarding_id"] = $each_list->clientOnboarding->id ?? null;
            array_push($datatable_rows, $each_row);

        }
        return $datatable_rows;

    }

    public function saveStatus($data)
    {

        $rfpid = $this->rfpModel->where('id', $data['id'])
            ->update(['rpf_status' => $data['rpf_status'], 'assign_resource_id' => $data['assign_resource_id']]);

        return $rfpid;
    }

    public function getDetails()
    {
        $rfp = DB::table('rfp_details')
            ->select('rfp_site_name', 'rfp_site_address', 'rfp_site_city', 'rfp_site_postalcode')->get();
        $rfp = collect($rfp)->last();
        return $rfp;

    }
    /**
     *  Function to destroy  rfp
     *
     *  @param empty
     *  @return  array
     *
     */
    public function destroyRfp($id)
    {
        return $this->rfpModel->find($id)->delete();
    }
    /**
     *  Function to save  rfp
     *
     *  @param empty
     *  @return  array
     *
     */
    public function storeRfp($request)
    {

        $submission_deadline_date = $request->submission_deadline_date;
        $submission_deadline_time = date("G:i:s", strtotime($request->submission_deadline_time));

        $data = array(
            'rfp_response_type_id' => $request->rfp_response_type_id,
            'employee_id' => $request->employee_id,
            'rfp_site_name' => $request->rfp_site_name,
            'rfp_site_address' => $request->rfp_site_address,
            'rfp_site_city' => $request->rfp_site_city,
            'rfp_site_postalcode' => $request->rfp_site_postalcode,
            'rfp_published_date' => $request->rfp_published_date,
            'site_visit_available' => intval($request->site_visit_available),
            'site_visit_deadline' => $request->site_visit_deadline,
            'q_a_deadline_available' => intval($request->q_a_deadline_available),
            'qa_deadline' => $request->qa_deadline,
            'submission_deadline' => $submission_deadline_date . " " . $submission_deadline_time,
            'announcement_date' => $request->announcement_date,
            'project_start_date' => $request->project_start_date,
            'rfp_contact_name' => $request->rfp_contact_name,
            'rfp_contact_title' => $request->rfp_contact_title,
            'rfp_contact_address' => $request->rfp_contact_address,
            'rfp_phone_number' => $request->rfp_phone_number,
            'rfp_email' => $request->rfp_email,
            'total_annual_hours' => $request->total_annual_hours,
            'scope_summary' => $request->scope_summary,
            'force_required' => $request->force_required,
            'term' => $request->term,
            'option_renewal' => $request->option_renewal,
            'unique_id' => '12ty',
            'site_unionized' => $request->site_unionized,
            'union_name' => $request->union_name,
            'summary_notes' => $request->summary_notes,
            'created_by' => \Auth::user()->id,

        );

        $rfp_details = $this->rfpModel->updateOrCreate(array('id' => $request->id), $data);
        $rfptrackingarray = ["RFP Summary Entered","RFP Approved And Resources Allocated",
                            "Template Downloaded","Site Visit","Question & Answer","Pricing Model","First Draft Completed",
                        "Insurance, WSIB and Other Documents Completed","Review Session With Executive","Revisions And Edits","Submit RFP"];
        
        $this->rfpResponseSubmissionDate->where('rfp_details_id', $request->id)->delete();
        
        if ($request->submission_label_name != null) {
            $submission_date_details = array_combine($request->submission_label_name, $request->submission_label_value);
            
            foreach ($submission_date_details as $key => $value) {
                $submission_date = $this->rfpResponseSubmissionDate->create([
                    'rfp_details_id' => $rfp_details->id,
                    'response_submission_other_date_label' => $key,
                    'response_submission_other_date_value' => $value,
                ]
                );
            }
        }
        
        $rfptracking = ["rfp_details_id"=>$rfp_details->id,"rfp_process_steps_id"=>1,
        "completion_date"=>date("Y-m-d"),"notes"=>"Created","entered_by_id"=>\Auth::user()->id];
        RfpTrackingStage::create($rfptracking);
        $this->rfpProjectExecutionDate->where('rfp_details_id', $request->id)->delete();
        
        if ($request->execution_label_name != null) {
            $execution_date_details = array_combine($request->execution_label_name, $request->execution_label_value);
            foreach ($execution_date_details as $key => $value) {
                $execution_date = $this->rfpProjectExecutionDate->create([
                    'rfp_details_id' => $rfp_details->id,
                    'project_execution_other_date_label' => $key,
                    'project_execution_other_date_value' => $value,
                ]
                );
            }
        }
        
        $this->rfpEvaluationCriteria->where('rfp_details_id', $request->id)->delete();
        
        for ($i = 0; $i < count($request->criteria_name); $i++) {
            $this->rfpEvaluationCriteria->create([
                'rfp_details_id' => $rfp_details->id,
                'criteria_name' => $request->criteria_name[$i],
                'points' => $request->points[$i],
                'notes' => $request->notes[$i],
            ]
            );
        }
        return $rfp_details->id;
    }

    /**
     *  Function to save  rfp
     *
     *  @param empty
     *  @return  array
     *
     */
    public function getUniqueRFPKey($request, $rfp_details_id)
    {
        $rfp_site_name = substr($this->removeSpace($request->get('rfp_site_name')), 0, 3);
        $unique_key = strtoupper($rfp_site_name . $rfp_details_id);
        return $unique_key;
    }

    /**
     *  Function to save  rfp
     *
     *  @param empty
     *  @return  array
     *
     */
    public function updateUniqueRFPKey($unique_id, $rfp_details_id)
    {
        return $this->rfpModel->where('id', $rfp_details_id)->update(['unique_id' => $unique_id]);
    }

    /**
     * Remove spaces,Special Characters and multiple hyphens
     *
     * @param  $string
     * @return $string
     */
    public function removeSpace($string)
    {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public function getRfpDetails($rfp_id)
    {
        $rfpDetails = $this->rfpModel->where('id', $rfp_id)->first();
        return $rfpDetails;
    }

    public function getRfpWinLose($rfp_id) {
        return $this->rfpDetailsWinLose->where('rfp_details_id',$rfp_id)->get();
    }
    public function saveTrackingStep($rfp_id, $request)
    {
        //dd($request->all());
        try {
            \DB::beginTransaction();
            $user = \Auth::user();
            $rfpDetails = $this->rfpModel->where('id', $rfp_id);

            if ($rfpDetails->count() > 0) {
                $completion_dates = $request->get('completion_date');
                $notes = $request->get('notes');
                $entered_by_ids = $request->get('entered_by_id');

                if (is_array($completion_dates)) {
                    foreach ($completion_dates as $tracking_id => $completion_date) {
                        if (isset($completion_date) || isset($entered_by_ids[$tracking_id]) || isset($notes[$tracking_id])) {
                            if (!isset($completion_date)) {
                                return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["completion_date." . $tracking_id => ["Please select the date"]]], 422);
                            }
                            if (!isset($entered_by_ids[$tracking_id])) {
                                return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["entered_by_id." . $tracking_id => ["Please select the person"]]], 422);
                            }
                            $data['rfp_details_id'] = $rfp_id;

                            $data['rfp_process_steps_id'] = $tracking_id;
                            $data['completion_date'] = $completion_dates[$tracking_id];
                            $data['notes'] = isset($notes[$tracking_id]) ? $notes[$tracking_id] : '--';
                            $data['entered_by_id'] = $entered_by_ids[$tracking_id];
                            $this->rfpTrackingStage->updateOrCreate(array('rfp_details_id' => $rfp_id, 'rfp_process_steps_id' => $tracking_id), $data);
                        }
                    }
                }

                \DB::commit();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Please select the rfp']);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

    }
    public function updateStatus($data)
    {

        $rfpWinLose = $this->rfpDetailsWinLose->where('rfp_details_id', $data['id'])->update(['status' => $data['status'], 'rfp_debrief_attended' => $data['rfp_debrief_attended'],
            'rfp_debrief_attended_no' => $data['rfp_debrief_attended_no'], 'did_we_take_it' => $data['did_we_take_it'], 'did_we_take_it_no' => $data['did_we_take_it_no'], 'offered_by_the_client_no' => $data['offered_by_the_client_no']]);
        return $rfpWinLose;
    }
    public function getUserLookupByPermission($permissions)
    {
        $users = User::when(($permissions != null), function ($query) use ($permissions) {
            $query->permission($permissions);

        });
        $user_lookup = $users->orderBy('first_name','asc')->get();
        $user_lookup = $user_lookup->pluck('name_with_emp_no', 'id');
        $return_list = $user_lookup->toArray();

        return $return_list;

    }

    public function getForm($id)
    {
        return $this->rfpModel->where('id', $id)->with('evaluationCriteria', 'projectExecutionDates', 'responseSubmissionDates')->first();
    }

    public function deleteRfpTracking($lookup_id, $rfp_id)
    {
        try {
            \DB::beginTransaction();
            $this->rfpTrackingStage->where('rfp_process_steps_id', '=', $lookup_id)
                ->where('rfp_details_id', '=', $rfp_id)
                ->delete();
            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

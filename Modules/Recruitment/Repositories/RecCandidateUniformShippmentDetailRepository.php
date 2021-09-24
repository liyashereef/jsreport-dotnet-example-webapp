<?php

namespace Modules\Recruitment\Repositories;

use Carbon\Carbon;
use Modules\Recruitment\Models\RecCandidateUniformShippmentDetail;
use App\Repositories\MailQueueRepository;
use Modules\Recruitment\Repositories\RecCandidateTrackingRepository;
use App\Services\HelperService;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Recruitment\Models\RecJob;
use Modules\Recruitment\Models\RecCandidateUniformShippmentDetailStatusLog;

class RecCandidateUniformShippmentDetailRepository
{
    public function __construct(
        RecCandidateUniformShippmentDetail $recCandidateUniformShippmentDetail,
        RecCandidateTrackingRepository $recCandidateTrackingRepository,
        MailQueueRepository $mailQueueRepository,
        RecCandidateUniformShippmentDetailStatusLog $recCandidateUniformShippmentDetailStatusLog
    ) {
        $this->model = $recCandidateUniformShippmentDetail;
        $this->recCandidateUniformShippmentDetailStatusLog=$recCandidateUniformShippmentDetailStatusLog;
        $this->recCandidateTrackingRepository=$recCandidateTrackingRepository;
        $this->mailQueueRepository = $mailQueueRepository;
    }

    public function getList()
    {
        $data=$this->model->whereHas('candidate')->with(['candidate', 'customerUniformKit','shippmentDetailsLog.createdUser'])->get();
        return $this->prepareArr($data);
    }

    public function prepareArr($data)
    {
        $doc = array();
        foreach ($data as $key => $value) {
            $status_arr=array();
            $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $value->candidate_id)->first();
            if (isset($job_details)) {
                $job=RecJob::find($job_details->job_id);
                $customer_name= $job->customer->client_name;
                $each_doc['id'] = $value->id;
                $each_doc['candidate_id'] = $value->candidate_id;
                $each_doc['customer_name'] = isset($value->customerUniformKit->customer)?$value->customerUniformKit->customer->client_name:$customer_name;
                $each_doc['candidate_name'] = $value->candidate->name;
                $each_doc['shippment_address'] = $value->shippment_address;
                $each_doc['kit_name'] = isset($value->customerUniformKit->kit_name)?$value->customerUniformKit->kit_name:'--';
                $each_doc['kit_id'] = $value->kit_id;
                $each_doc['shippment_status'] = $value->shippment_status;
                $each_doc['status_date_time'] = $value->status_date_time;
               
                if ($value->shippmentDetailsLog->isEmpty()) {
                    $status='--';
                    $user_fullname='--';
                    $date='--';
                    $time='--';
                    $status_arr[] = array("status" => $status, "user_name" => $user_fullname, "date" => $date, "time" => $time);
                }
                foreach ($value->shippmentDetailsLog as $each_status) {
                    $status =config('globals.shipping_status')[$each_status->status];
                    $user = $each_status->createdUser->full_name;
                    $user_fullname = (isset($each_status->createdUser)) ? $each_status->createdUser->first_name . ' ' . $each_status->createdUser->last_name : "";
                    if (isset($each_status->datetime)) {
                        $date_obj = Carbon::parse($each_status->datetime);
                        $date = $date_obj->format('F d, Y');
                        $time = $date_obj->format('h:i A');
                    } else {
                        $date = "--";
                        $time = "--";
                    }
                    // $notes = ($each_status->incident_status_list_id != 1) && ($each_status->notes) ? $each_status->notes : "--";
                     $final_status = $status;
                     $status_arr[] = array("status" => $status, "user_name" => $user_fullname, "date" => $date, "time" => $time);
                  
                    // $status_updated = $each_status->updated_at;
                }
                $each_doc['status'] = $status_arr;
                array_push($doc, $each_doc);
            }
        }
        return $doc;
    }




    public function saveShippingStatus($data)
    {
        $statusLog=array();
        $data['status_date_time'] = Carbon::now()->toDateTimeString();
        $details=$this->model->find($data['id']);
        $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $details['candidate_id'])->first();
        if ($data['shippment_status']==2) {
            $this->recCandidateTrackingRepository->saveTracking($details['candidate_id'], "uniform_processed", false, $job_details->job_id);
            $uniformDetails=$this->model->with(['candidate', 'customerUniformKit.customer'])->find($data['id']);
            if (isset($uniformDetails->customerUniformKit)) {
                $helper_variable = array(
                    '{receiverFullName}' => HelperService::sanitizeInput($uniformDetails->candidate->name),
                    '{projectNumber}' => HelperService::sanitizeInput($uniformDetails->customerUniformKit->customer->project_number),
                    '{client}' => HelperService::sanitizeInput($uniformDetails->customerUniformKit->customer->client_name),
                     '{loggedInUserEmployeeNumber}' => \Auth::user()->employee->employee_no,
                     '{loggedInUser}' => \Auth::user()->getFullNameAttribute(),
                );
                $emailResult = $this->mailQueueRepository
                    ->prepareMailTemplate(
                        "rec_uniform_shipment_mail",
                        null,
                        $helper_variable,
                        "Modules\Recruitment\Models\RecCandidateUniformShippmentDetail",
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        $uniformDetails->candidate_id
                    );
            }
        } elseif ($data['shippment_status']==3) {
            $checkRecordExists=$this->recCandidateTrackingRepository->checkRecordExists($details['candidate_id']);
            if (!$checkRecordExists) {
                $this->recCandidateTrackingRepository->saveTracking($details['candidate_id'], "uniform_received", false, $job_details->job_i);
            }
        }
        $statusLog['rec_candidate_uniform_shippment_details_id']=$data['id'];
        $statusLog['status']=$data['shippment_status'];
        $statusLog['datetime']=\Carbon::now();
        $statusLog['created_by']=\Auth::user()->id;
        $result= $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $this->recCandidateUniformShippmentDetailStatusLog->create($statusLog);
    }
}

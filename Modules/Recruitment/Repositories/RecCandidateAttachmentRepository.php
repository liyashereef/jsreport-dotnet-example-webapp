<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecCandidateAttachmentLookup;
use Modules\Recruitment\Models\RecCandidateAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Recruitment\Models\RecJob;

class RecCandidateAttachmentRepository
{
    public function getAttachmentApi($request)
    {
        $attachments = RecCandidateAttachmentLookup::where('job_id', null)->orWhere('job_id', $request->jobId)->
          with(['candidateAttachment' => function ($q) {
            $q->where('candidate_id', '=', Auth::user()->id);
          }])->get();
          $job=RecJob::find($request->jobId);
        return $this->prepareData($attachments, $job);
    }

    public function prepareData($data, $job)
    {
        
        $attachments_arr = array();
        $required_attachment=json_decode($job->required_attachments);
        foreach ($data as $attachments => $value) {
            $each_attachment['id'] = $value['id'];
            $each_attachment['job_id'] = $value['job_id'];
            $each_attachment['attachment_id'] = $value['attachment_id'];
            if (!empty($required_attachment) && in_array($value['id'], $required_attachment)) {
                $each_attachment['mandatory']=1;
            } else {
                $each_attachment['mandatory']=0;
            }
            $each_attachment['attachment_name'] = $value['attachment_name'];

            $each_attachment['attachment_file_name'] ="";
            foreach ($value['candidateAttachment'] as $key => $val) {
                $each_attachment['candidate_id'] = $val['candidate_id'];
                $each_attachment['attachment_file_name'] = $val['attachment_file_name'];
                $each_attachment['submitted_file_path'] = \Storage::disk('s3-recruitment')->temporaryUrl($val['attachment_file_name'], Carbon::now()->addMinutes(60));

            }
            array_push($attachments_arr, $each_attachment);
        }
        return $attachments_arr;
    }

    public function storeAttachment($attchment_id,$candidate_id,$file_name)
    {
        $data['attachment_file_name'] = $file_name;
        $response = RecCandidateAttachment::updateOrCreate(array('attachment_id' => $attchment_id, 'candidate_id' => $candidate_id), $data);
        if($response->wasRecentlyCreated){
            return true;
        }else{
            return false;
        }
    }

    
    public function removeAttachment($id,$candidate_id)
    {
        RecCandidateAttachment::where('candidate_id', $candidate_id)->where('attachment_id', $id)->delete();
    }

}

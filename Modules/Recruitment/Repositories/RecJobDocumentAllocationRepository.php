<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecJobDocumentAllocation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RecJobDocumentAllocationRepository
{
    public function getDocumentApi($request)
    {
        $documents = RecJobDocumentAllocation::with([
            'documentAllocationWithTrashed' => function ($query) {
                $query->with(['onBoardingDocumentAllocation' => function ($qu) {
                        $qu->select('id')->with(['attachments'=> function ($q) {
                            $q->select('id', 'document_id', 'file_name', 'file_type');
                        }]);
                }
                ])
                ->select('id', 'document_id', 'document_name', 'order');
            }
        ])
        ->with(['documentJob' => function ($query) use ($request) {
            return $query->where('candidate_id', $request->candidateId);
        }])
        ->where('process_tab_id', $request->processTabId)
        ->where('job_id', $request->jobId)
        ->select('id', 'document_id', 'job_id', 'process_tab_id', 'display', 'is_mandatory')
        ->get()->toArray();
       // dd($documents);
        return $this->prepareData($documents);
    }

    public function prepareData($data)
    {
        
        $doc = array();
        foreach ($data as $jobDocumentAllocation => $value) {
            if ($value['display']==1) {
                $each_doc['id'] = $value['id'];
                $each_doc['document_id'] = $value['document_id'];
                $each_doc['job_id'] = $value['job_id'];
                $each_doc['process_tab_id'] = $value['process_tab_id'];
                $each_doc['display'] = $value['display'];
                $each_doc['is_mandatory'] = $value['is_mandatory'];
                $each_doc['document_name'] = $value['document_allocation_with_trashed']['document_name'];
                foreach ($value['document_allocation_with_trashed']['on_boarding_document_allocation']['attachments'] as $key => $val) {
                    $each_doc[$key]['file_name'] = $val['file_name'];
                    $each_doc[$key]['file_type'] = $val['file_type'];
                    if (($val['file_type'] == 1) && ($val['file_name'] !='')) {
                        $each_doc['video_name'] = $val['file_name'];
                        $each_doc['video_path'] = \Storage::disk('s3-recruitment')->temporaryUrl($val['file_name'], Carbon::now()->addMinutes(60));
                    } elseif (($val['file_type'] == 2) && ($val['file_name'] !='')) {
                        $each_doc['doc_name'] = $val['file_name'];
                        $each_doc['doc_path'] = \Storage::disk('s3-recruitment')->temporaryUrl($val['file_name'], Carbon::now()->addMinutes(60));
                    }
                }
                if (isset($value['document_job'])) {
                    $each_doc['submitted_file'] = $value['document_job']['file_name'];
                    $each_doc['submitted_file_id'] = $value['document_job']['id'];
                    $each_doc['submitted_file_path'] = \Storage::disk('s3-recruitment')->temporaryUrl($value['document_job']['file_name'], Carbon::now()->addMinutes(60));
                } else {
                    $each_doc['submitted_file'] = '';
                }
                array_push($doc, $each_doc);
            }
        }
        return $doc;
    }

    public function getJobDocumentDetails($id)
    {
        $documents = RecJobDocumentAllocation::where('id', $id)->first();
        return $documents;
    }

    public function getCandidateDocuments($candidate_id,$job_id)
    {
        $documents = RecJobDocumentAllocation::with([
            'documentAllocationWithTrashed' => function ($query) {
                $query->with(['onBoardingDocumentAllocation' => function ($qu) {
                        $qu->select('id')->with(['attachments'=> function ($q) {
                            $q->select('id', 'document_id', 'file_name', 'file_type');
                        }]);
                }
                ])
                ->select('id', 'document_id', 'document_name', 'order');
            }
        ])
        ->with(['documentJob' => function ($query) use ($candidate_id) {
            return $query->where('candidate_id', $candidate_id);
        }])
        ->where('job_id', $job_id)
        ->select('id', 'document_id', 'job_id')
        ->get()->toArray();
        return $documents;
    }
}

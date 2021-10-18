<?php

namespace Modules\Client\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Support\Str;

//Repo List
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Client\Repositories\VisitorLogScreeningSubmissionRepository;
use Modules\Client\Http\Resources\VisitorLogScreeningSubmissionResource;
use Modules\Client\Repositories\VisitorLogRepository;
use \Carbon\Carbon;

class VisitorLogScreeningSubmissionController extends Controller
{

    protected $visitorLogScreeningSubmissionRepository, $customerEmployeeAllocation;
    protected $customerReporsitory, $helperService, $visitorLogRepository;

    public function __construct(
        VisitorLogScreeningSubmissionRepository $visitorLogScreeningSubmissionRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocation,
        CustomerRepository $customerReporsitory,
        VisitorLogRepository $visitorLogRepository,
        HelperService $helperService
        ) {
            $this->repository = $visitorLogScreeningSubmissionRepository;
            $this->customerEmployeeAllocation = $customerEmployeeAllocation;
            $this->customerReporsitory = $customerReporsitory;
            $this->visitorLogRepository = $visitorLogRepository;
            $this->helperService = $helperService;
        }


    public function index(){
        //get allocted customers

        $customer_details_arr = $this->getAllocatedCustomers();

        if(\Auth::user()->can('view_all_customers_in_visitor_screening'))
        {
        $project_list= $this->customerReporsitory->getProjectsDropdownList('all');
        }
        else if(\Auth::user()->can('view_allocated_customers_in_visitor_screening'))
        {
        $project_list = $customer_details_arr->pluck('customer_name_and_number', 'id')->toArray();
        }else
        {
        $project_list = null;
        }
        $startdate = date('Y-m-d');
        $enddate = date('Y-m-d');
        return view('client::visitor-screening-submissions',compact('project_list','startdate','enddate'));
    }

    public function getAllocatedCustomers(){
        $allocated_customers_arr = $this->customerEmployeeAllocation->getAllocatedCustomers(Auth::user());
        $customer_details_arr = $this->customerReporsitory->getCustomers($allocated_customers_arr);
        return $customer_details_arr;
    }

    public function getList(Request $request){
        $inputs = $request->all();
        if($request->input('passed') == 3){
            $inputs['passed'] = null;
        }
        $inputs['customer_id'] = [];
        $result = [];
        if($request->filled('customer_id')){
            array_push($inputs['customer_id'],$request->input('customer_id'));
        }else{
            $customer_details_arr = $this->getAllocatedCustomers();
            $inputs['customer_id'] = $customer_details_arr->pluck('id');
        }
        $visitors = $this->visitorLogRepository->getScreenedEntries($inputs);
        $screeningEntries = $this->repository->getAllMyVisitorScreeningSubmissions($inputs);
        // $result['list'] = $this->formatScreeningData($request->input('passed'),$screeningEntries,$visitors);
        $formatedData = $this->formatScreeningData($request->input('passed'),$screeningEntries,$visitors);

        $result['list'] = $formatedData['list'];
        $result['questions'] = $formatedData['questionArray'];
        $result['totalScanned'] = sizeof($screeningEntries);
        $result['checkedInCount'] = collect($visitors)->where('checkout',null)->count();
        //$passed value `3` means Currently Check In
        if($request->input('passed') == null || $request->input('passed') == 3){
            $result['passedCount'] = collect($screeningEntries)->where('passed',1)->count();
            $result['failedCount'] = collect($screeningEntries)->where('passed',0)->count();
        }else{
            $inputs['passed'] = 1;
            $result['passedCount'] = $this->repository->getVisitorScreeningPassedCount($inputs);
            $inputs['passed'] = 0;
            $result['failedCount'] = $this->repository->getVisitorScreeningPassedCount($inputs);
            $result['totalScanned'] = (int)$result['passedCount']+(int)$result['failedCount'];
        }
        return $result;
    }

    public function formatScreeningData($passed,$screeningEntries,$visitors){
        $results = [];
        $questionArray = [];
        foreach($screeningEntries as $key=>$screeningEntry){
            $visitor = $visitors->where('visitor_log_screening_submission_uid',$screeningEntry->uid)->first();
            $insertFlag = true;
            //$passed value `3` means Currently Check In
            if($passed==3 && empty($visitor)){
                $insertFlag = false;
            }
            if($passed==3 && !empty($visitor) && $visitor->checkout != null){
                $insertFlag = false;
            }
            if($insertFlag){
                $results[$key]['id'] = $screeningEntry->id;
                $results[$key]['uid'] = $screeningEntry->uid;
                $results[$key]['passed'] = $screeningEntry->passed;
                $results[$key]['passed_str'] = ($screeningEntry->passed == 1)? 'Passed' : 'Failed';
                $results[$key]['created_at'] = (!empty($screeningEntry->screened_at))? Carbon::parse($screeningEntry->screened_at)->format('Y-m-d H:i:s'): '';
                $results[$key]['created_date'] = (!empty($screeningEntry->screened_at))? Carbon::parse($screeningEntry->screened_at)->format('M d Y'): '';
                $results[$key]['created_time'] = (!empty($screeningEntry->screened_at))? Carbon::parse($screeningEntry->screened_at)->format('H:i A'): '';
                $results[$key]['customer_id'] = $screeningEntry->customer_id;
                $results[$key]['client_name_and_number'] = $screeningEntry->customer->client_name_and_number;
                $results[$key]['visitor_log_screening_template_id'] = $screeningEntry->visitor_log_screening_template_id;
                $results[$key]['visitor_log_screening_template_name'] = $screeningEntry->VisitorLogScreeningTemplate->name;
                $results[$key]['screening_question_answers'] =  $screeningEntry->visitorLogScreeningSubmissionQuestionAnswersWithTrashed;
                $results[$key]['visitor_id'] = (!empty($visitor))? $visitor->id : '';
                $results[$key]['visitor_name'] = (!empty($visitor))? $visitor->first_name.' '.$visitor->last_name  : '';

                foreach($screeningEntry->visitorLogScreeningSubmissionQuestionAnswersWithTrashed as $question){
                    if($question->visitor_log_screening_template_question_str){
                        if (!in_array($question->visitor_log_screening_template_question_str, $questionArray)){
                            // $questionArray[$question->visitor_log_screening_template_question_id] = $question->visitor_log_screening_template_question_str;
                            array_push($questionArray,$question->visitor_log_screening_template_question_str);
                        }
                    }

                }
            }
        }
        $return['list'] = $results;
        $return['questionArray'] = $questionArray;
        return $return;
    }

    public function getAttemptedQuestionAndAnswers($submissionId)
    {
        return $this->repository->getAttemptedQuestionAndAnswers($submissionId);
    }

}

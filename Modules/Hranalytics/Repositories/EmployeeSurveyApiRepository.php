<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use Auth;
use Illuminate\Support\Arr;
use Modules\Admin\Http\Requests\Request;
use Modules\Hranalytics\Models\EmployeeSurveyTemplate;
use Modules\Hranalytics\Models\EmployeeSurveyQuestion;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Hranalytics\Models\EmployeeSurveyEntry;
use Modules\Hranalytics\Models\EmployeeSurveyAnswer;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class EmployeeSurveyApiRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    public function __construct(
        UserRepository $userRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        HelperService $helperService
    ) {

        $this->helperService = $helperService;
        $this->userRepository = $userRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }
    /**
     * To get Open survey against a user using auth id
     *
     * @param [type] $request templateid
     * @return void
     */
    public function getTemplatedetail($request)
    {

        $employeerating = EmployeeRatingLookup::all()->sortByDesc("score");
        $ratings = [];

        $templateid = $request->templateid;
        $employeesurvey = EmployeeSurveyTemplate::with([
            "employeesurveycustomerallocation",
            "employeesurveyroleallocation",
            "employeesurveycustomerallocation.Customer"
        ])
            ->find($templateid);
        $returnArray["surveyId"] = $employeesurvey->id;
        $returnArray["surveyName"] = $employeesurvey->survey_name;
        foreach ($employeerating as $emprating) {
            // $ratings[$emprating->id]["id"] = $emprating->id;
            // $ratings[$emprating->id]["name"] = $emprating->rating;
            //$ratings = array_merge($ratings, ["id" => $emprating->id, "name" => $emprating->rating]);
            $ratings[] = ["id" => $emprating->id, "name" => $emprating->rating];
        }
        $yesOrNoRating = [["id" => "yes", "name" => "Yes"], ["id" => "no", "name" => "No"]];
        //$returnArray["options"]["yes/no"] = ["yes" => "yes", "no" => "no"];
        // $returnArray["options"]["rating"] = $ratings;
        $employeequestions = EmployeeSurveyQuestion::where("survey_id", $templateid)->with('templateAnswer')->get()->sortBy("sequence");
        $questionArray = [];

        foreach ($employeequestions as $questions) {
            if ($questions->answer_type == 1) {
                $options = $yesOrNoRating;
            } else {
                $options = $ratings;
            }
            $questionArray[] = [
                "id" => $questions->id,
                "question" => $questions->question,
                "answerType" => (int)$questions->answer_type,
                "sequence" => $questions->sequence,
                "options" => $options,
                "required" => true,
                "inputType" => null


            ];

            // $questionArray[]["id"] = $questions->id;
            // $questionArray[]["question"] = $questions->question;
            // $questionArray[]["answerType"] = $questions->answer_type;
            // $questionArray[]["sequence"] = $questions->sequence;
        }
        $returnArray["questions"] = $questionArray;
        return response()->json($returnArray);
    }
    /**
     * To get Open survey against a user using auth id
     *
     * @param [type] $request
     * @return void
     */

    public function getEmployeeRatings($request)
    {
        $user = Auth::user()->id;
        $user_roles = ((Auth::user()->roles->pluck('id'))->toArray());
        $allocatedcustomereloq = CustomerEmployeeAllocation::with('Customer')
            ->where([["user_id", $user]])->get();
        $allocatedcustomers = [];
        $customerdetails = [];
        foreach ($allocatedcustomereloq as $alloccustomer) {
            array_push($allocatedcustomers, $alloccustomer->customer_id);
            $customerdetails[$alloccustomer->customer_id]["name"] = $alloccustomer->customer->project_number
                . "-" .
                $alloccustomer->customer->client_name;
        }
        $returnArray = [];
        $employeeratingsgeneral = EmployeeSurveyTemplate::with([
            "employeesurveycustomerallocation",
            "employeesurveyroleallocation",
            "employeesurveycustomerallocation.Customer",
            "templateEntries" => function ($q) {
                $userid = \Auth::user()->id;
                return $q->where("user_id", $userid);
            }
        ])
            ->where([
                ["start_date", '<=', date("Y-m-d")],
                ["expiry_date", '>=', date("Y-m-d")], ["active", 1]
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        $i = 0;
        foreach ($employeeratingsgeneral as $generalsurveys) {
            $entrycount = ($generalsurveys->templateEntries)->count();
            if ($entrycount > 0) {
                $submitted = 1;
            } else {
                $submitted = 0;
            }
            if ($generalsurveys->customer_based == 0) {
                if ($generalsurveys->role_based == 0) {
                    $returnArray[$i]["survey_id"] = $generalsurveys->id;
                    $returnArray[$i]["customer_id"] = 0;
                    $returnArray[$i]["customer_name"] = "General Survey";
                    $returnArray[$i]["survey_name"] = $generalsurveys->survey_name;
                    $returnArray[$i]["start_date"] = $generalsurveys->start_date;
                    $returnArray[$i]["expiry_date"] = $generalsurveys->expiry_date;
                    $returnArray[$i]["submitted"] = $submitted;
                    $i++;
                } else {
                    $allocatedroles = ($generalsurveys->employeesurveyroleallocation)->pluck("role_id")->toArray();
                    $rolesintersected = array_intersect($user_roles, $allocatedroles);
                    if (count($rolesintersected) > 0) {
                        $returnArray[$i]["survey_id"] = $generalsurveys->id;
                        $returnArray[$i]["customer_id"] = 0;
                        $returnArray[$i]["customer_name"] = "General Survey";
                        $returnArray[$i]["survey_name"] = $generalsurveys->survey_name;
                        $returnArray[$i]["start_date"] = $generalsurveys->start_date;
                        $returnArray[$i]["expiry_date"] = $generalsurveys->expiry_date;
                        $returnArray[$i]["submitted"] = $submitted;
                        $i++;
                    }
                }
            } else {
                $allocatedroles = ($generalsurveys->employeesurveyroleallocation)
                    ->pluck("role_id")->toArray();
                $surveyallocatedcustomers = ($generalsurveys->employeesurveycustomerallocation)
                    ->pluck("customer_id")->toArray();

                $customerarrayintersection = array_intersect($surveyallocatedcustomers, $allocatedcustomers);
                if (count($customerarrayintersection) > 0) {
                    foreach ($customerarrayintersection as $key => $value) {
                        $customername = $customerdetails[$value]["name"];
                        if ($generalsurveys->role_based == 0) {
                            $returnArray[$i]["survey_id"] = $generalsurveys->id;
                            $returnArray[$i]["customer_id"] = $value;
                            $returnArray[$i]["customer_name"] = $customername;
                            $returnArray[$i]["survey_name"] = $generalsurveys->survey_name;
                            $returnArray[$i]["start_date"] = $generalsurveys->start_date;
                            $returnArray[$i]["expiry_date"] = $generalsurveys->expiry_date;
                            $returnArray[$i]["submitted"] = $generalsurveys->templateEntries->where('customer_id', $value)->count()>0?1:0;
                            $i++;
                        } else {
                            $allocatedroles = ($generalsurveys->employeesurveyroleallocation)
                                ->pluck("role_id")->toArray();
                            $rolesintersected = array_intersect($user_roles, $allocatedroles);
                            if (count($rolesintersected) > 0) {
                                $returnArray[$i]["survey_id"] = $generalsurveys->id;
                                $returnArray[$i]["customer_id"] = $value;
                                $returnArray[$i]["customer_name"] = $customername;
                                $returnArray[$i]["survey_name"] = $generalsurveys->survey_name;
                                $returnArray[$i]["start_date"] = $generalsurveys->start_date;
                                $returnArray[$i]["expiry_date"] = $generalsurveys->expiry_date;
                                $returnArray[$i]["submitted"] = $generalsurveys->templateEntries->where('customer_id', $value)->count()>0?1:0;
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        return response()->json($returnArray);
    }

    /**
     * To get Open survey against a user using auth id
     *
     * @param [type] $request templateid
     * @return void
     */
    public function submitEmployeeSurvey($request)
    {

        $surveyEntry['customer_id'] = $request->customerId;
        $surveyEntry['survey_id'] = $request->surveyId;
        $surveyEntry['user_id'] = Auth::id();
        $surveyEntry['created_by'] = Auth::id();
        $entry = EmployeeSurveyEntry::create($surveyEntry);
        $answers = json_decode($request->get('answers'));
        foreach ($answers as $questionId => $each_answer) {
            $questionArray["question_id"] = $questionId;
            $questionArray["entry_id"] = $entry->id;
            $questionArray['survey_id'] = $request->surveyId;
            $questionArray["question"] = EmployeeSurveyQuestion::find($questionId)->question;
            $questionArray["answer_type"] = EmployeeSurveyQuestion::find($questionId)->answer_type;
            $questionArray["answer"] = $each_answer;
            $questionArray['created_by'] = Auth::id();
            EmployeeSurveyAnswer::create($questionArray);
        }
        return response()->json($entry);
    }

    public function fetchSurveydetails($request)
    {
        $employeerating = EmployeeRatingLookup::pluck('rating', 'id')->toArray();
        $data = EmployeeSurveyEntry::with('survey', 'customer', 'user', 'surveyAnswer', 'surveyAnswer.surveyRating')->where('survey_id', $request->surveyId)->where('customer_id', $request->customerId)->where('user_id', Auth::id())->first();
        $survey_entries=array();
        if (isset($data->surveyAnswer)) {
            foreach ($data->surveyAnswer as $key => $each_survey_entry) {
                $survey_entries[$key]['question']=$each_survey_entry['question'];
                $survey_entries[$key]['question_id']=$each_survey_entry['question_id'];
                if ($each_survey_entry['answer_type']==1) {
                    $survey_entries[$key]['answer']=ucfirst($each_survey_entry['answer']);
                } else {
                    $survey_entries[$key]['answer']=$employeerating[$each_survey_entry['answer']];
                }
            }
        }
            return response()->json($survey_entries);
    }
}

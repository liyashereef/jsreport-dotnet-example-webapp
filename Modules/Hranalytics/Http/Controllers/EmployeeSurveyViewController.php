<?php

namespace Modules\Hranalytics\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\EmployeeRatingLookup;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Hranalytics\Models\EmployeeSurveyEntry;
use Modules\Hranalytics\Models\EmployeeSurveyTemplate;
use Modules\Hranalytics\Repositories\EmployeeSurveyRepository;

class EmployeeSurveyViewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    protected $customeremployeeallocationrepository;
    protected $contractsrepository;
    protected $payperiodrepository;
    protected $clientSurveyrepository;
    protected $userRepository;
    protected $employeeRatingRepository;
    public function __construct(
        CustomerEmployeeAllocationRepository $customeremployeeallocationrepository,
        ContractsRepository $contractsrepository,
        PayPeriodRepository $payperiodrepository,
        UserRepository $userRepository,
        EmployeeSurveyRepository $employeeRatingRepository
    ) {
        $this->customeremployeeallocationrepository = $customeremployeeallocationrepository;
        $this->contractsrepository = $contractsrepository;
        $this->payperiodrepository = $payperiodrepository;
        $this->userRepository = $userRepository;
        $this->employeeRatingRepository = $employeeRatingRepository;
    }
    public function index()
    {
        $permission = "";
        $permissionChart = "";
        $clientAllocation = [];
        $areaManagers = [];
        if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_employee_surveys"])) {
            $permission = "all";
            $areaManagers = CustomerEmployeeAllocation::with('user')
                ->whereHas("areaManager")->get();
        } elseif (\Auth::user()->hasAnyPermission(["view_allocated_employee_surveys"])) {
            $clientAllocation = $this->customeremployeeallocationrepository->getAllocatedCustomerId([\Auth::user()->id]);
            $areaManagers = CustomerEmployeeAllocation::with(['user' => function ($q) {
                return $q->orderBy("first_name", "asc");
            }])
                ->whereIn("customer_id", $clientAllocation)
                ->whereHas("areaManager")
                ->whereHas("user", function ($q) {
                    return $q->orderBy("first_name", "asc");
                })->get();
        }
        $employeeSurveys = $this->employeeRatingRepository->getEmployeeSurveyList();
        $aremanagersarray = [];
        foreach ($areaManagers as $rmanager) {
            $aremanagersarray[$rmanager->user->id] = $rmanager->user->getFullNameAttribute();
        }

        if (\Auth::user()->hasAnyPermission(["view_all_employee_surveys"])) {
            $permissionChart = "all";
        } elseif (\Auth::user()->hasAnyPermission(["view_allocated_employee_surveys"])) {
            $clientAllocationView = $this->customeremployeeallocationrepository->getAllocatedCustomerId([\Auth::user()->id]);
        }
        $employeeratinglookup = EmployeeRatingLookup::all()->sortBy("score");
        $clients = Customer::select('id', 'client_name', 'project_number')->when(
            $permission != "all",
            function ($q) use ($clientAllocation) {
                return $q->whereIn("id", $clientAllocation);
            }
        )->orderBy("client_name", "asc")->get();
        $i = 0;
        $allocationarray = [];
        foreach ($clients as $client) {
            array_push($allocationarray, $client->id);
            $clientarray[$i]["id"] = $client->id;
            $clientarray[$i]["parent_client_id"] = 0;
            $clientarray[$i]["subs"] = [];
            $clientarray[$i]["title"] = $client->project_number . " - " . $client->client_name;
            $i++;
        }

        $clientarray = json_encode($clientarray, true);
        $permissionaddclients = Customer::select('id', 'client_name')->when(
            $permissionChart != "all",
            function ($q) use ($clientAllocation) {
                return $q->whereIn("id", $clientAllocation);
            }
        )->orderBy("client_name", "asc")->get();
        $chartclients = Customer::select('id', 'client_name')->when(
            $permission != "all",
            function ($q) use ($clientAllocation) {
                return $q->whereIn("id", $clientAllocation);
            }
        )->orderBy("client_name", "asc")->get();
        asort($aremanagersarray);
        $templatecustomerarray = [];
        $viewallocatedarray = [];
        foreach ($employeeSurveys as $survey) {
            if ($survey->employeesurveycustomerallocation->count() > 0) {
                $customeralloc = ($survey->employeesurveycustomerallocation);
                $survallocation = ($customeralloc->pluck("customer_id")->toArray());
                if (array_intersect($survallocation, $allocationarray)) {
                    $viewallocatedarray[] = ["id" => $survey->id, "name" => $survey->survey_name];
                }
                foreach ($customeralloc as $customer) {
                    if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_clientsurvey"])) {
                        $templatecustomerarray[$customer->customer_id][] =
                            ["id" => $survey->id, "name" => $survey->survey_name];
                    } else {
                        if (array_intersect($survallocation, $allocationarray)) {
                            $templatecustomerarray[$customer->customer_id][] =
                                ["id" => $survey->id, "name" => $survey->survey_name];
                        }
                    }
                }
            } else {
                $templatecustomerarray[0][] = ["id" => $survey->id, "name" => $survey->survey_name];
                $viewallocatedarray[] = ["id" => $survey->id, "name" => $survey->survey_name];
            }
        }
        return view('hranalytics::employeeSurveys.index', compact(
            "clients",
            "permissionaddclients",
            "clientarray",
            "employeeratinglookup",
            "areaManagers",
            "aremanagersarray",
            "employeeSurveys",
            "templatecustomerarray",
            "viewallocatedarray",
            "permission"
        ));
    }
    public function getSurveyblock(Request $request)
    {
        $templateid = $request->surveys;
        $client = $request->client;
        $templates = EmployeeSurveyTemplate::with([
            'templateForm'
        ])
            ->whereIn("id", $templateid)->get();
        $templatequestion = [];
        $templatenames = [];
        foreach ($templates as $template) {
            $templatenames[] = ["id" => $template->id, "name" => $template->survey_name];
            $templatequestions = $template->templateForm;
            foreach ($templatequestions as $templatequestioncollection) {
                $templatequestion[$templatequestioncollection->survey_id][] = [
                    "id" => $templatequestioncollection->id,
                    "survey_id" => $templatequestioncollection->survey_id,
                    "question" => $templatequestioncollection->question,
                    "answer_type" => $templatequestioncollection->answer_type
                ];
            }
            //$templatequestion["id"][]=[]
        }

        return view(
            "hranalytics::employeeSurveys.employeesurveyanalytics",
            compact("templatenames", "templatequestion")
        );
    }

    public function plotGraph(Request $request)
    {
        $templateid = $request->templateid;
        $client = $request->client;
        $returnarray = [];
        $xaxis = [];
        $arrayindexbarchart = [];
        $barchartreferencelookup = [];

        $employeeratinglookupasc = EmployeeRatingLookup::all()->sortByDesc("score");
        foreach ($employeeratinglookupasc as $emprating) {
            $arrayindexbarchart[] = $emprating->id;
            $barchartreferencelookup[$emprating->id] = $emprating->rating;
        }
        //$returnarray["barlookup"] = $barchartreferencelookup;
        $questions = EmployeeSurveyTemplate::with([
            'templateForm',
            'templateEntries' => function ($q) use ($templateid) {
                return $q->where("survey_id", $templateid);
            },
            "templateForm.templateAnswer" => function ($qryans) {
                //
            },
            "templateForm.templateAnswer.surveyCustomer" => function ($qry) use ($client) {
                if (isset($client)) {
                    return $qry->whereIn("customer_id", $client);
                }
            }
        ])
            ->find($templateid);


        $tquestions = $questions->templateForm;

        if ($tquestions->count() > 0) {
            foreach ($tquestions as $tquestion) {
                $returnarray[$tquestion->id]["answer_type"] = $tquestion->answer_type;
                if ($tquestion->answer_type == 1) {
                    $returnarray[$tquestion->id]["chart"] = "pie";
                    $returnarray[$tquestion->id]["data"]["YES"] = [];
                    $returnarray[$tquestion->id]["data"]["NO"] = [];
                } elseif ($tquestion->answer_type == 2) {
                    $returnarray[$tquestion->id]["chart"] = "bar";
                }

                if (count($tquestion->templateAnswer) > 0) {
                    if ($tquestion->answer_type == 1) {
                        foreach ($tquestion->templateAnswer as $pieanswer) {
                            if ($pieanswer->answer == "yes") {
                                $returnarray[$tquestion->id]["data"]["YES"][] = "YES";
                            } else {
                                $returnarray[$tquestion->id]["data"]["NO"][] = "NO";
                            }
                        }
                    } elseif ($tquestion->answer_type == 2) {
                        $returnarray[$tquestion->id]["question"] = $tquestion->question;
                        foreach ($arrayindexbarchart as $key => $lookupid) {
                            $returnarray[$tquestion->id]["data"][$lookupid] = 0;
                        }
                        foreach ($tquestion->templateAnswer as $chartanswer) {
                            $canswer = $chartanswer->answer;

                            foreach ($arrayindexbarchart as $key => $lookupid) {
                                if ($lookupid == $canswer) {
                                    $returnarray[$tquestion->id]["data"][$lookupid] = $returnarray[$tquestion->id]["data"][$lookupid] + 1;
                                } else {
                                }
                            }
                        }
                    }
                    //dd($tquestion->templateAnswer);
                }
            }
        }

        return json_encode([$returnarray, $barchartreferencelookup], true);
    }

    public function plotData(Request $request)
    {
        $returnarray = [];
        $templateid = $request->surveys;
        $client = $request->client;

        $xaxis = [];
        $questions = EmployeeSurveyTemplate::with([
            'templateForm',
            'templateEntries' => function ($q) use ($templateid) {
                return $q->where("survey_id", $templateid);
            },
            "templateForm.templateAnswer" => function ($qryans) {
                //
            },
            "templateForm.templateAnswer.surveyCustomer" => function ($qry) use ($client) {
                if (isset($client)) {
                    return $qry->whereIn("customer_id", $client);
                }
            },
        ])
            ->find($templateid);
        $form = $questions ? $questions->templateForm : [];
        if (!empty($form)) {
            $totalratingarray = [];

            foreach ($form as $quest) {
                if ($quest->id == 10) {
                    //  dd($quest->templateAnswer);
                }
                if (count($quest->templateAnswer) > 0) {
                    foreach ($quest->templateAnswer as $ans) {
                        $totalratingarray[$ans->question_id]["answer_type"] = $ans->answer_type;
                        if (isset($totalratingarray[$ans->question_id][$ans->answer]["count"])) {
                            $totalratingarray[$ans->question_id][$ans->answer]["count"] =
                                $totalratingarray[$ans->question_id][$ans->answer]["count"] + 1;
                        } else {
                            $totalratingarray[$ans->question_id][$ans->answer]["count"] = 1;
                        }
                    }
                } else {
                    $totalratingarray[$quest->id]["answer_type"] = $quest->answer_type;
                    $totalratingarray[$quest->id][$quest->answer]["count"] = 0;
                }
                $xaxis[] = ["id" => $quest->id, "question" => $quest->question, "answer_type" => $quest->answer_type];
            }
            $employeeratinglookupasc = EmployeeRatingLookup::all()->sortBy("score");
            $employeeratinglookup = EmployeeRatingLookup::all()->sortBy("score");
            $dataplotarray = [];
            $yesnoratingarray = [];
            $answervaluearray = [0 => "yes", 1 => "no"];
            $answertextarray = [0 => "yes", 1 => "no"];
            foreach ($employeeratinglookup as $rlook) {
                array_push($yesnoratingarray, null);
                $answervaluearray[] = $rlook->id;
                $answertextarray[] = $rlook->rating;
            }
            //dd($answervaluearray);
            $questionwisecombination = [];
            foreach ($totalratingarray as $key => $value) {
                $anstype = $value["answer_type"];
                $questdata = $form->find($key);
                $questiontext = $questdata->question;
                $questid = $questdata->id;
                if ($anstype == 1) {
                    $yesvalue = 0;
                    if (isset($value["yes"])) {
                        $yesvalue = $value["yes"]["count"];
                    }
                    $novalue = 0;
                    if (isset($value["no"])) {
                        $novalue = $value["no"]["count"];
                    }

                    $mergedresult = array_merge([$yesvalue, $novalue], $yesnoratingarray);
                    $questionwisecombination[$questid] = $mergedresult;
                    $dataplotarray[] = [
                        "name" => $questiontext,
                        "data" => $mergedresult
                    ];
                } elseif ($anstype == 2) {
                    $yesvalue = null;
                    $novalue = null;
                    $yesno = [null, null];
                    $rating = [null, null];
                    foreach ($employeeratinglookupasc as $rat) {
                        $ratingid = $rat->id;
                        //dd($totalratingarray[$questid]);
                        if (isset($totalratingarray[$questid][$ratingid])) {
                            array_push($rating, $totalratingarray[$questid][$ratingid]["count"]);
                        } else {
                            array_push($rating, 0);
                        }

                        // dd($id);
                    }
                    $questionwisecombination[$questid] = $rating;
                    $dataplotarray[] = [
                        "name" => $questiontext,
                        "data" => $rating
                    ];
                }
            }
            //dd($questionwisecombination);


            $returnarray["questions"] = $form;
            $returnarray["statistics"] = ($totalratingarray);
            $returnarray["rating"][1] = ["yes", "no"];
            $returnarray["rating"][2] = $employeeratinglookup;
            $returnarray["totalplotdata"] = json_encode($dataplotarray, true);
            $returnarray["answerarray"] = $answervaluearray;
            $returnarray["questionwisecombination"] = $questionwisecombination;
            $returnarray["answertextarray"] = $answertextarray;
        } else {
            $employeeratinglookup = null;
            $returnarray["questions"] = null;
            $returnarray["statistics"] = null;
            $returnarray["rating"][1] = ["yes", "no"];
            $returnarray["rating"][2] = [];
            $returnarray["totalplotdata"] = [];
            $returnarray["answerarray"] = [];
            $returnarray["questionwisecombination"] = [];
            $returnarray["answertextarray"] = [];
        }

        return json_encode($returnarray, true);
    }

    public function getSurveyData(Request $request)
    {

        $permission = "";
        $time = strtotime("-1 year", time());
        $empSurveyData = [];
        if ($request->has("startdate")) {
            $startdate = $request->get("startdate");
        } else {
            $startdate = date("Y-m-d", $time);
        }

        if ($request->has("enddate")) {
            $enddate = $request->get("enddate");
        } else {
            $enddate = date("Y-m-d", $time);
        }

        $clientAllocation = [];
        $userCount = User::all()->count();
        if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_employee_surveys"])) {
            $permission = "all";
        } elseif (\Auth::user()->hasAnyPermission(["view_allocated_employee_surveys"])) {
            $clientAllocation = $this->customeremployeeallocationrepository
                ->getAllocatedCustomerId([\Auth::user()->id]);
            $clientAllocation = array_merge($clientAllocation, [0]);
        }
        $datearray = [];
        $payperiodids = [];
        $payperiods = $this->payperiodrepository->getPayperiodRangeAll($startdate, $enddate);
        foreach ($payperiods as $payp) {
            array_push($payperiodids, $payp->id);
            if (!in_array($payp->short_name, $datearray)) {
                array_push($datearray, $payp->short_name);
            }
        }
        try {
            $sData = EmployeeSurveyEntry::select("*")
                ->addSelect(\DB::raw("(select concat_ws('-',project_number,client_name)
                 from customers where id=employee_survey_entries.customer_id) as client"))
                ->addSelect(\DB::raw("(select survey_name from employee_survey_templates
                 where id=employee_survey_entries.survey_id) as survey_name"))
                ->with(["customer", "survey"])
                ->when($permission != "all", function ($q) use ($clientAllocation) {
                    return $q->whereIn("customer_id", $clientAllocation);
                })
                // ->whereIn("payperiod", $payperiodids)
                ->whereBetween("created_at", [$startdate, $enddate])
                ->orderBy('created_at', 'desc')
                ->get()->groupBy("survey_id");
            $empsurveyData = [];
            foreach ($sData as $value) {
                $empValue = $value[0];
                $customerBased = $empValue->survey->customer_based;
                $roleBased = $empValue->survey->role_based;

                $expectedResponse = "customer based";
                if ($customerBased < 1 && $roleBased > 0) {
                    $Roles = $empValue->survey->roleAllocation->pluck("role_id")->toArray();
                    $expectedResponse = User::whereHas("roles", function ($q) use ($Roles) {
                        return $q->whereIn("id", $Roles);
                    })->count();
                } else  if ($customerBased > 0 && $roleBased < 1) {
                    $Customers = $empValue->survey
                        ->employeesurveycustomerallocation->pluck("customer_id")->toArray();

                    $expectedResponse = User::whereHas("allocation", function ($q) use ($Customers) {
                        return $q->whereIn("customer_id", $Customers);
                    })->count();
                    //$expectedResponse = "Customer-" . $expectedResponse;
                } else  if ($customerBased > 0 && $roleBased > 0) {
                    $Roles = $empValue->survey->roleAllocation->pluck("role_id")->toArray();
                    $Customers = $empValue->survey
                        ->employeesurveycustomerallocation->pluck("customer_id")->toArray();
                    $expectedResponse = User::whereHas("allocation", function ($q) use ($Customers) {
                        return $q->whereIn("customer_id", $Customers);
                    })->whereHas("roles", function ($q) use ($Roles) {
                        return $q->whereIn("id", $Roles);
                    })->count();
                    $expectedResponse =  $expectedResponse;
                } else  if ($customerBased < 1 && $roleBased < 1) {
                    $expectedResponse = $userCount;
                }
                $empSurveyData[] = [
                    "id" => $empValue->survey->id,
                    "surveyName" => $empValue->survey_name,
                    "Responses" => count($value),
                    "expectedResponse" => $expectedResponse
                ];
            }
            //dd($empsurveyData);
            return datatables()->of($empSurveyData)->addIndexColumn()->toJson();
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getSurveyDataDetailed(Request $request)
    {

        $permission = "";
        $time = strtotime("-1 year", time());
        if ($request->has("startdate")) {
            $startdate = $request->get("startdate");
        } else {
            $startdate = date("Y-m-d", $time);
        }

        if ($request->has("enddate")) {
            $enddate = $request->get("enddate");
        } else {
            $enddate = date("Y-m-d", $time);
        }
        $survey_id = $request->survey_id;
        $clientAllocation = [];
        if (\Auth::user()->hasAnyPermission(["super_admin", "view_all_employee_surveys"])) {
            $permission = "all";
        } elseif (\Auth::user()->hasAnyPermission(["view_allocated_employee_surveys"])) {
            $clientAllocation = $this->customeremployeeallocationrepository
                ->getAllocatedCustomerId([\Auth::user()->id]);
            $clientAllocation = array_merge($clientAllocation, [0]);
        }
        $datearray = [];
        $payperiodids = [];
        $payperiods = $this->payperiodrepository->getPayperiodRangeAll($startdate, $enddate);
        foreach ($payperiods as $payp) {
            array_push($payperiodids, $payp->id);
            if (!in_array($payp->short_name, $datearray)) {
                array_push($datearray, $payp->short_name);
            }
        }
        try {
            $data = EmployeeSurveyEntry::select("*")
                ->addSelect(\DB::raw("(select concat_ws('-',project_number,client_name) from customers where id=employee_survey_entries.customer_id) as client"))
                ->addSelect(\DB::raw("(select concat_ws(' ',first_name,last_name) from users where id=employee_survey_entries.user_id) as usercontact"))
                ->with(["customer", "user", "created_user", "user.employee", "user.employee.employeePosition", "survey"])
                ->when($permission != "all", function ($q) use ($clientAllocation) {
                    //if (Auth::user()->can("view_all_employee_surveys")) {
                    return $q->whereIn("customer_id", $clientAllocation);
                    //}
                })
                // ->whereIn("payperiod", $payperiodids)
                ->whereBetween("created_at", [$startdate, $enddate])
                ->where("survey_id", $survey_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return datatables()->of($data)->addIndexColumn()->toJson();
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hranalytics::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('hranalytics::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('hranalytics::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    public function detailedView($id)
    {
        $data = EmployeeSurveyEntry::with('survey', 'customer', 'user', 'surveyAnswer', 'surveyAnswer.surveyRating')->find($id);
        return view('hranalytics::employeeSurveys.detailedSurvey', compact('data'));
    }
}

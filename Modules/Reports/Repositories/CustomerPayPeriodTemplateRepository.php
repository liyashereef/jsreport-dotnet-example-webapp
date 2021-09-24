<?php

namespace Modules\Reports\Repositories;

use Auth;
use DB;
use Modules\Admin\Models\LeaveReason;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\Template;
use Modules\Admin\Models\TemplateForm;
use Modules\Admin\Models\TemplateQuestionsCategory;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Supervisorpanel\Models\CustomerPayperiodTemplate;
use Modules\Supervisorpanel\Models\CustomerReport;
use Modules\Supervisorpanel\Models\CustomerReportAdhoc;
use Modules\Supervisorpanel\Repositories\CustomerReportAdhocRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use View;

class CustomerPayPeriodTemplateRepository
{   
    protected $customer_map_repository;
    protected $customer_report_repository;

    public function __construct(
        CustomerMapRepository $customer_map_repository,
        CustomerReportRepository $customer_report_repository
    ){
        $this->customer_map_repository = $customer_map_repository;
        $this->customer_report_repository = $customer_report_repository;
    }


    public function getCustomerSurveyReport($payperiodid) {
        //dd($payperiodid);
        $template = [];
        $toDisplay = [];
        $template = $this->getTemplateId($payperiodid);
        //dd($template);

       if ($template['count'] === 0) {
           //no result or answer empty
           //dd('result empty');
           return 0;
       } else if ($template['count'] === 1) {
           //have same template
           $categoryIDs = [];
           $mainQuestionIDs = [];
           $subQuestionIDs = [];

           //Get Category Id, Main Question id, sub Question id
           $AllId = $this->questionCategoryId($template['id']);
           $categoryIDs = $AllId[0];
           $mainQuestionIDs = $AllId[1];
           $subQuestionIDs = $AllId[2];

           //Question
           $mainQuestion = $this->getMainQuestion($template['id']);
           //dd($mainQuestion);

           $questionChecker = $this->getQuestionChecker($mainQuestion);
           //dd($questionChecker);

           $completeQuestion[] = $this->getCompleteQuestionArray($mainQuestion, $template['id']);
           //dd($completeQuestion);

           //Client and Answer Query
            $clientDetailsMainAnswer = $this->getClientDetailsMainAnswer($payperiodid, $mainQuestionIDs);
            //dd($clientDetailsMainAnswer);

            //Client Details
            $clientDetails = $this->getClientArray($clientDetailsMainAnswer);
            //dd($clientDetails);

            //main answer
            $mainAnswerChecker = $this->getMainAnswer($clientDetailsMainAnswer);
            //dd($mainAnswerChecker);
            
            //SubAnswer Array
            $subQuestionChecker = $this->getSubQuestionChecker($template['id']);
            //dd($subQuestionChecker);
            $subAnswer = $this->subQuestionArray($payperiodid, $subQuestionIDs);
            //dd($subAnswer);
            $subAnswerChecked = $this->subQuestionChecking($clientDetails, $subQuestionChecker, $subAnswer);
            //dd($subQuestionChecker, $subAnswer, $subAnswerChecked);
            //dd($subAnswerChecked);

            //category checking
            $categoryCheckedMainAnswer = $this->categoryChecking($clientDetails, $questionChecker, $mainAnswerChecker);
            //dd($categoryCheckedMainAnswer);

            //main Question to Answer Checking
            $mainQuestionToAnswerCheckedMainAnswer = $this->mainQuestionToAnswerChecking($clientDetails, $questionChecker, $categoryCheckedMainAnswer);
            foreach($mainQuestionToAnswerCheckedMainAnswer as $ky => $value) {
                ksort($mainQuestionToAnswerCheckedMainAnswer[$ky]);
            }
            //dd($mainQuestionToAnswerCheckedMainAnswer);

            //CompleteAnswer
            $completeMixedAnswer = $this->answerMixer($clientDetails, $mainQuestionToAnswerCheckedMainAnswer, $subAnswerChecked, $completeQuestion);

            
            $toDisplay['question'] = $completeQuestion;
            $toDisplay['answer'] = $completeMixedAnswer;
            //dd($completeQuestion, $completeMixedAnswer);
            return $toDisplay;

       } else {
           //have different template result can't be shown
           return 1;
       }
    }
    
    //template Id
    public function getTemplateId($payperiodid) {
        $template = CustomerPayperiodTemplate::where('payperiod_id', $payperiodid)
                                ->join('templates', 'customer_payperiod_templates.template_id', '=', 'templates.id')
                                //->where('templates.active', 1)
                                ->where('templates.deleted_at', null)
                                ->where('customer_payperiod_templates.deleted_at', null)
                                ->select('template_id')
                                ->groupBy('template_id')
                                ->get();
        $templateDetails = [];
        $templateDetails['count'] = count($template);
        if (count($template) != 0) {
            $templateDetails['id'] = $template[0]['template_id'];
        }
        return $templateDetails;
    }

    //get main questions
    public function getMainQuestion($templateId) {
        $mainQuestion = DB::table('template_forms')
                            ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                            ->where('template_id', $templateId)
                            ->where('parent_position', null)
                            ->where('template_forms.deleted_at', null)
                            ->where('template_questions_categories.deleted_at', null)
                            ->orderBy('description')
                            ->select('template_forms.id','multi_answer','position','template_forms.question_category_id','description','question_text')
                            ->get();
        return $mainQuestion;
    }

    //question Checker
    public function getQuestionChecker($mainQuestion) {
        $prevQuestionCatChecker = 1;
        $questionChecker;

        foreach($mainQuestion as $key=>$value) {
            $questCatCheck = $value->description;
            if ($prevQuestionCatChecker  != $questCatCheck) {
                $prevQuestionCatChecker = $questCatCheck;
                $i=0;
            }
            $questionChecker[$prevQuestionCatChecker][$value->id]['id']= $value->id;
            $questionChecker[$prevQuestionCatChecker][$value->id]['multi_answer']= $value->multi_answer;
            $questionChecker[$prevQuestionCatChecker][$value->id]['position']= $value->position;
            $questionChecker[$prevQuestionCatChecker][$value->id]['question_category_id']= $value->question_category_id;
            $questionChecker[$prevQuestionCatChecker][$value->id]['description']= $value->description;
            $questionChecker[$prevQuestionCatChecker][$value->id]['question_text'] = $value->question_text;
        }
        return $questionChecker;
   }

    //complete questions
    public function getCompleteQuestionArray($mainQuestion, $templateId)  {
        $mainQuestionText;
        $multiAnswer;
        $parentPosition;
        $prevQuestionCategory = 0;
        $completeQuestionArray = [];

        foreach ($mainQuestion as $key => $value) {
            $mainQuestionText = $value->question_text;
            $multiAnswer = $value->multi_answer;
            $parentPosition = $value->position;
            $questionCategory = $value->question_category_id;

            if ($questionCategory != $prevQuestionCategory) {
                $completeQuestionArray[] = "Question Category";
                $completeQuestionArray[] = "Category Average";
                $prevQuestionCategory = $questionCategory;
            }

            $subQueQuery = DB::table('template_forms')
                                        ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                                        ->where('template_id', $templateId)
                                        ->where('parent_position', $parentPosition)
                                        ->where('template_forms.deleted_at', null)
                                        ->where('template_questions_categories.deleted_at', null)
                                        ->select('answer_type_id', 'question_text')
                                        ->get();
        
            if ($multiAnswer == 0) {
                    $completeQuestionArray[] = $mainQuestionText;
                    $subQuesitonQuery = $subQueQuery;
                    foreach ($subQuesitonQuery as $key => $subQuestion) {
                        $completeQuestionArray[] = $subQuestion->question_text;
                    }
            } else {
                    $completeQuestionArray[] = $mainQuestionText;
                    $multiQuesitonQuery = $subQueQuery;
                    $multiQuestion= [];
                    $multiQuestionAdhoc = [];
                    $answerType;
                    foreach ($multiQuesitonQuery as $key => $value) {
                        $answerType = $value->answer_type_id;
                        if ($value->answer_type_id == 4 ) {
                            $multiQuestionAdhoc[] = [$value->question_text,"Date of absenteeism","Hours Booked Off","Reason","Notes"];
                        } else {
                            $multiQuestion[] = $value->question_text;
                        }
                    }
                        if ($answerType == 4) {
                                for ($i = 0; $i < 10; $i++) {
                                    foreach ($multiQuestionAdhoc as $key => $value) {
                                        foreach ($value as $k=>$v) {
                                            $completeQuestionArray[] = $v;
                                        }
                                    }
                                }
                        } else {
                                for ($i = 0; $i < 10; $i++) {
                                    foreach ($multiQuestion as $key => $value) {
                                        $completeQuestionArray[] = $value;
                                    }
                                }
                        }
                    }
        }
        return $completeQuestionArray;
   }
   //Question Part Ends

   //ClientDetailsMainAnswer Part Starts
   public function getClientDetailsMainAnswer($payperiod_id, $mainQuestionIDs) {
        $clientDetailsMainAnswer = DB::table('customer_payperiod_templates')
                    ->join('customers', 'customer_payperiod_templates.customer_id','=','customers.id')
                    ->join('users', 'customer_payperiod_templates.created_by', '=', 'users.id')
                    ->join('employees', 'customer_payperiod_templates.created_by', '=', 'employees.user_id')
                    ->join('pay_periods', 'customer_payperiod_templates.payperiod_id', '=', 'pay_periods.id')
                    ->join('customer_reports', 'customer_reports.customer_payperiod_template_id','=','customer_payperiod_templates.id')
                    ->join('template_forms','template_forms.id', '=', 'customer_reports.element_id')
                    ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                    ->where('payperiod_id',$payperiod_id)
                    ->where('template_forms.parent_position', null)
                    ->where('template_forms.deleted_at', null)
                    ->where('customer_reports.deleted_at', null)
                    ->where('customer_payperiod_templates.deleted_at', null)
                    ->whereIn('element_id', $mainQuestionIDs)
                    ->orderBy('customers.id')
                    ->orderBy('template_forms.question_category_id')
                    ->select(
                        'customer_payperiod_templates.payperiod_id',
                        'customers.id',
                        'customers.client_name',
                        'customers.project_number', 
                        DB::raw('CONCAT(IFNULL(users.first_name,"")," ",IFNULL(users.last_name,"")) as employee_name'), 
                        'employee_no as employee_number',
                        'pay_period_name',
                        'customer_reports.element_id',
                        'customer_reports.answer',
                        'template_forms.multi_answer',
                        'template_forms.position',
                        'template_questions_categories.description',
                        'template_forms.question_category_id')
                    ->get();
        return $clientDetailsMainAnswer;
    }
    //ClientDetailsMainAnswer Part Ends Here

    //client starts
    public function getClientArray($clientDetailsMainAnswer) {
        $preClient = -1;
        $clientdetailsArray = [];
        $i =0;
        foreach ($clientDetailsMainAnswer as $key => $value) {
            $client = $value->id;
            if($client != $preClient) {
                $preClient = $client;
                $clientdetailsArray[$i]['id'] = $value->id;
                $clientdetailsArray[$i]['client_name'] = $value->client_name;
                $clientdetailsArray[$i]['project_number'] = $value->project_number;
                $clientdetailsArray[$i]['employee_name'] = $value->employee_name;
                $clientdetailsArray[$i]['employee_number'] = $value->employee_number;
                $clientdetailsArray[$i]['payperiod_id'] = $value->payperiod_id;
                $clientdetailsArray[$i++]['pay_period_name'] = $value->pay_period_name;
            }
        }
        return $clientdetailsArray;
    }
   //client end

    //main answer starts here
    public function getMainAnswer($clientDetailsMainAnswer) {
        $preAnswerChecker = 1;
        $client = 0;
        $answerChecker = [];
        $j=-1;
          foreach ($clientDetailsMainAnswer as $key => $value) {
            $ansCatCheck = $value->description;
            if ($preAnswerChecker != $ansCatCheck) {
                $preAnswerChecker = $ansCatCheck;
                $i=0;
            }
            if($client != $value->id) {
                $client = $value->id;
                $j++;
            }
            // $answerChecker[$j][$preAnswerChecker][$i]['element_id'] = $value->element_id;
            // $answerChecker[$j][$preAnswerChecker][$i]['answer'] = $value->answer;
            // $answerChecker[$j][$preAnswerChecker][$i]['multi_answer'] = $value->multi_answer;
            // $answerChecker[$j][$preAnswerChecker][$i]['position'] = $value->position;
            // $answerChecker[$j][$preAnswerChecker][$i]['description'] = $value->description;
            // $answerChecker[$j][$preAnswerChecker][$i++]['question_category_id'] = $value->question_category_id;

            $answerChecker[$j][$preAnswerChecker][$value->element_id]['element_id'] = $value->element_id;
            $answerChecker[$j][$preAnswerChecker][$value->element_id]['answer'] = $value->answer;
            $answerChecker[$j][$preAnswerChecker][$value->element_id]['multi_answer'] = $value->multi_answer;
            $answerChecker[$j][$preAnswerChecker][$value->element_id]['position'] = $value->position;
            $answerChecker[$j][$preAnswerChecker][$value->element_id]['description'] = $value->description;
            $answerChecker[$j][$preAnswerChecker][$value->element_id]['question_category_id'] = $value->question_category_id;
          }
          return $answerChecker;
       }

       public function getSubQuestionChecker($templateId) {
        $subQuestionChecker = DB::table('template_forms')
                             ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                             ->where('template_id', $templateId)
                             ->where('template_forms.parent_position','!=',null)
                             ->where('template_forms.deleted_at', null)
                             ->where('template_questions_categories.deleted_at', null)
                             ->select('template_forms.id','multi_answer','position','parent_position','question_category_id','description','question_text','answer_type_id')
                             ->orderBy('template_forms.question_category_id')
                             ->get();
        $parent_position = 0; 
            foreach ($subQuestionChecker as $key => $value) {
                if ($parent_position != $value->parent_position) {
                    $parent_position = $value->parent_position;
                    $m = 0;
                }
                                 
                $subQuestionCheckerArray[$parent_position][$m]['id'] = $value->id;
                $subQuestionCheckerArray[$parent_position][$m]['answer_type_id'] = $value->answer_type_id;
                $subQuestionCheckerArray[$parent_position][$m++]['question_text'] = $value->question_text;
                     
            }
        return $subQuestionCheckerArray;
       }

       public function subQuestionArray($payperiodid, $subQuestionIDs) {

        $subAnswer = DB::table('customer_payperiod_templates')
                            ->join('customers', 'customer_payperiod_templates.customer_id','=','customers.id')
                            ->join('users', 'customer_payperiod_templates.created_by', '=', 'users.id')
                            ->join('employees', 'customer_payperiod_templates.created_by', '=', 'employees.user_id')
                            ->join('pay_periods', 'customer_payperiod_templates.payperiod_id', '=', 'pay_periods.id')
                            ->join('customer_reports', 'customer_reports.customer_payperiod_template_id','=','customer_payperiod_templates.id')
                            ->join('template_forms','template_forms.id', '=', 'customer_reports.element_id')
                            ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                            ->where('payperiod_id',$payperiodid)
                            ->where('template_forms.parent_position','!=',null)
                            ->where('template_forms.deleted_at',null)
                            ->where('template_questions_categories.deleted_at', null)
                            ->where('customer_reports.deleted_at', null)
                            ->where('customer_payperiod_templates.deleted_at', null)
                            ->whereIn('element_id', $subQuestionIDs)
                            ->select(
                                'customers.id',
                                'customer_reports.answer',
                                'customer_reports.element_id',
                                'template_forms.parent_position',
                                'template_forms.answer_type_id',
                                'template_forms.question_category_id')
                            ->get();
            //dd($subAnswer);                
            $customer_id = 0;
            $parent_position = 0;
            $subAnswerArray = [];
            foreach ($subAnswer as $key => $value) {
                if($customer_id != $value->id) {
                    $customer_id = $value->id;
                    $m =0;
                }
                if($parent_position != $value->parent_position) {
                    $parent_position = $value->parent_position;
                    $m = 0;
                }
    
                $subAnswerArray[$customer_id][$parent_position][$m]['answer_type_id'] = $value->answer_type_id;
                $subAnswerArray[$customer_id][$parent_position][$m]['element_id'] = $value->element_id;
                $subAnswerArray[$customer_id][$parent_position][$m++]['answer'] = $value->answer;
            }
            return $subAnswerArray;
       }

       public function subQuestionChecking($clientDetails, $subQuestionChecker, $subAnswer) {
           //dd($subQuestionChecker, $subAnswer);
           foreach ($clientDetails as $key => $value) {
               if(!array_key_exists($value['id'], $subAnswer)) {
                   $subAnswer[$value['id']] = [];
               }
           }
           //dd($subAnswer);
           foreach ($clientDetails as $client=>$clientId) {
              foreach ($subQuestionChecker as $key => $value) {
                  if(!array_key_exists("$key", $subAnswer[$clientId['id']])) {
                      $subAnswer[$clientId['id']][$key] = [];
                  }
              }
           }
           //dd($subAnswer);
           foreach ($clientDetails as $client=>$clientId) {
                foreach ($subQuestionChecker as $questionNumber => $q) {
                   foreach ($q as $key => $value) {
                       if (!array_key_exists("$key", $subAnswer[$clientId['id']][$questionNumber])) {
                        $subAnswer[$clientId['id']][$questionNumber][$key]['element_id'] = $value['id'];
                        $subAnswer[$clientId['id']][$questionNumber][$key]['answer'] = null;
                        $subAnswer[$clientId['id']][$questionNumber][$key]['answer_type_id'] = $value['answer_type_id'];
                       }
                   }
                }
            }

        //dd($subQuestionChecker, $subAnswer);
         return $subAnswer;
        }

       public function categoryChecking($clientDetails, $questionChecker, $mainAnswerChecker) {
           //add category if it doesn't exsist
            foreach ($clientDetails as $k => $value) {
                foreach ($questionChecker as $key => $value) {
                    if(!array_key_exists("$key",$mainAnswerChecker[$k])) {
                        $mainAnswerChecker[$k][$key]=[];
                    }
                }
            }
            return $mainAnswerChecker;
       }

       public function mainQuestionToAnswerChecking($clientDetails, $questionChecker, $mainAnswerChecker) {
          foreach ($clientDetails as $clientNumber => $clientDetails) {
             foreach ($questionChecker as $categoryName => $details) {
                foreach ($details as $key => $value) {
                    if(!array_key_exists("$key", $mainAnswerChecker[$clientNumber][$categoryName])) {
                        $mainAnswerChecker[$clientNumber][$categoryName][$key]['element_id'] = $value['id'];
                        $mainAnswerChecker[$clientNumber][$categoryName][$key]['answer'] = null;
                        $mainAnswerChecker[$clientNumber][$categoryName][$key]['multi_answer'] = $value['multi_answer'];
                        $mainAnswerChecker[$clientNumber][$categoryName][$key]['position'] = $value['position'];
                        $mainAnswerChecker[$clientNumber][$categoryName][$key]['description'] = $value['description'];
                        $mainAnswerChecker[$clientNumber][$categoryName][$key]['question_category_id'] = $value['question_category_id'];
                    }
                }
             }
          }
          return $mainAnswerChecker;
       }

       public function answerMixer($clientDetails, $mainAnswerChecker,$subAnswer, $completeQuestion) {
        $completeAnswerArray = [];
        for ($q = 0; $q <count($clientDetails); $q++) {
            $answerArray = [];

            $answerArray[]= $clientDetails[$q]['client_name'];
            $answerArray[]= $clientDetails[$q]['project_number'];
            $answerArray[]= $clientDetails[$q]['employee_name'];
            $answerArray[]= $clientDetails[$q]['employee_number'];
            $answerArray[]= $clientDetails[$q]['pay_period_name'];
    
            $payperiod_id = $clientDetails[$q]['payperiod_id'];
            $customer_id = $clientDetails[$q]['id'];
            $client_id = $clientDetails[$q]['id'];
    
    
            $mainAnswerText;
            $multiAnswerQuestion;
            $parentQuestionPosition;
            $prevAnswerCategoryname = 0;
            $avgCat = $this->customer_map_repository->getPayperiodAvgReport($client_id, [$payperiod_id]);
    
    
            foreach ($mainAnswerChecker[$q] as $key => $value) {
                foreach ($value as $k => $val) {
                    $mainAnswerText = $val['answer'];
                    $multiAnswerQuestion =  $val['multi_answer'];
                    $parentQuestionPosition = $val['position'];
                    $questionCategoryId = $val['question_category_id'];
    
                    if ($prevAnswerCategoryname != $questionCategoryId) {
                        $answerArray[] = $val['description'];
                        $des = $val['description'];
                        if (!array_key_exists("$des", $avgCat['score'])) {
                            $answerArray[] = 0;
                        } else {
                            $answerArray[] = round($avgCat['score'][$val['description']], 3);
                        }
                        $prevAnswerCategoryname = $questionCategoryId;
                    }
    
                    if ($multiAnswerQuestion === 0) {
                        $answerArray[] = $mainAnswerText;
                        if(!empty($subAnswer[$customer_id][$parentQuestionPosition])) {
                            foreach ($subAnswer[$customer_id][$parentQuestionPosition] as $key => $value) {
                                //$answerArray[] = $value['answer'];
                                if ($value['answer_type_id'] == 2) {
                                    $answerArray[] = $value['answer'];
                                }
                                if ($value['answer_type_id'] == 3) {
                                    if ($value['answer'] != null) {
                                        $userDetail =  User::find($value['answer']);
                                        if(!empty($userDetail)) {
                                            $answerArray[] = $userDetail->first_name." ".$userDetail->last_name."( ".$userDetail->trashedEmployee->employee_no." )" ;
                                        } else {
                                            $answerArray[] = null;
                                        }
                                    } else {
                                        $answerArray[] = $value['answer'];
                                    }
                                }
                                if ($value['answer_type_id'] == 4) {
                                    if ($value['answer'] != null) {
                                        $usresult = CustomerReportAdhoc::find($value['answer']);
                                        if (!empty($usresult)) {
                                            $user = User::find($usresult->employee_id);
                                            if(!empty($user->first_name)) {
                                                $answerArray[] = $user->first_name . " " . $user->last_name . "( " . $user->trashedEmployee->employee_no." )" ;
                                            } else {
                                                $answerArray[] = null;
                                            }
                                        }
                                    } else {
                                        $answerArray[] = $value['answer']; 
                                    }
                                }
                            }
                        }
                        
                    } else {
                        $answerArray[] = $mainAnswerText;
                        $multiQuestionAnswerQuery = $subAnswer[$customer_id][$parentQuestionPosition];
                        
                        $multiQ= [];
                        $multiQAdhocA = [];
    
                    foreach ($multiQuestionAnswerQuery as $key => $value) {
                        $questionType = $value['answer_type_id'];
    
                        if ($questionType == 4) {
                            if($value['answer'] != null) {
                                $usresult = CustomerReportAdhoc::find($value['answer']);
                                if (!empty($usresult)) {
                                    $user = User::find($usresult->employee_id);
                                    if(!empty($user->first_name)) {
                                        $multiQAdhocA[] = $user->first_name . " " . $user->last_name . "( " . $user->trashedEmployee->employee_no." )" ;
                                    } else {
                                        $multiQAdhocA[] = null;
                                    }
        
                                    if (!empty($usresult->date)) {
                                        $multiQAdhocA[] = date("d-M-Y", strtotime($usresult->date));
                                    } else {
                                        $multiQAdhocA[] = null;
                                    }
                                    
                                    if (!empty($usresult->hours_off)) {
                                        $multiQAdhocA[] = $usresult->hours_off;
                                    } else {
                                        $multiQAdhocA[] = null;
                                    }
        
                                    if ($usresult->reason_id > 0 && !empty($usresult->reason_id)) {
                                        $leavereasons = LeaveReason::find($usresult->reason_id);
                                        if($leavereasons){
                                            $multiQAdhocA[] = $leavereasons->reason;
                                        }else{
                                            $multiQAdhocA[] = "";
                                        }
                                        
                                        } else {
                                        $multiQAdhocA[] = null;
                                    }
        
                                    if (!empty($usresult->notes)) {
                                        $multiQAdhocA[] = $usresult->notes;
                                    } else {
                                        $multiQAdhocA[] = null;
                                    }
                                }
                            }
    
                        } else {
                                if($questionType == 3){
                                    $userDetail =  User::find($value['answer']);
                                    if(!empty($userDetail)) {
                                    $multiQ[] = $userDetail->first_name." ".$userDetail->last_name."( ".$userDetail->trashedEmployee->employee_no." )" ;
                                    }
                                }else{
                                    $multiQ[] = $value['answer'];
                                }
                        }
                    }
    
                    if($questionType == 4) {
                        $adhocFiller = [];
                        for ($i = 0; $i <50; $i++) {
                            $adhocFiller[] = null;
                        }
                        $filledAdhocAnswer = [];
                        $sliceMultiAdocA = array_slice($multiQAdhocA,0,50);
                        $filledAdhocAnswer = array_replace($adhocFiller, $sliceMultiAdocA);
                        foreach ($filledAdhocAnswer as $key => $value) {
                            $answerArray[] = $value;
                        }
                    } else {
                        $a = array_slice($multiQ,0, count($multiQ)/2);
                        $b = array_slice($multiQ, count($multiQ)/2);
                        $n1 = count($a);
                        $n2 = count($b);
                        $i = 0; 
                        $j = 0; 
                        $k = 0; 
                        $mixedAnswer = array();
                        while ($i < $n1 && $j < $n2) 
                        { 
                            $mixedAnswer[$k++] = $a[$i++]; 
                            $mixedAnswer[$k++] = $b[$j++]; 
                        }
                            $filler = [];
                            for($i = 0; $i <20; $i++) {
                                $filler[] = null;
                            }
                            $filledAnswer = [];
                            $mixedslice = array_slice($mixedAnswer,0,20);
                            $filledAnswer = array_replace($filler, $mixedslice);
                            foreach($filledAnswer as $key=>$value) {
                                $answerArray[] = $value;
                            }
                    }
                    
                    
                    }
                }    
                
            } //anwer for each ends here
    
    
            $answerArray[] = round($avgCat['score']['total'], 3);
            //$completeAnswerArray[] = $answerArray;

            $QSize = count($completeQuestion[0]) + 6;
            for($i = 0; $i <$QSize; $i++) {
                $blankAnswer[] = null;
            }
            $ans = array_replace($blankAnswer, $answerArray);
            $completeAnswerArray[] = array_slice($ans, 0, $QSize);
            
        }//for
        //dd($completeAnswerArray);
        return $completeAnswerArray;
       }

       public function questionCategoryId($templateId) {
           $preCategoryid = -1;
           $category = [];
           $categoryInArray = [];
           $mainQuestionInArray = [];
           $subQuestionInArray = [];

            $templateDetails = DB::table('template_forms')
                                ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                                ->where('template_id', $templateId)
                                ->where('template_forms.deleted_at', null)
                                ->where('template_questions_categories.deleted_at', null)
                                ->select('template_forms.id','template_forms.position', 'template_forms.question_category_id', 'template_forms.parent_position')
                                ->get();

            foreach ($templateDetails as $key => $value) {
                if($preCategoryid != $value->question_category_id) {
                    array_push($category, $value->question_category_id);
                    $preCategoryid = $value->question_category_id;
                }

                if($value->parent_position == null) {
                    array_push($mainQuestionInArray, $value->id);
                } else {
                    array_push($subQuestionInArray, $value->id);
                }
            }
            $categoryInArray = array_unique($category);

            $whereIn = [];
            $whereIn[0] = $categoryInArray;
            $whereIn[1] = $mainQuestionInArray;
            $whereIn[2] = $subQuestionInArray;

            return $whereIn;
       }


}
<?php

namespace Modules\Supervisorpanel\Repositories;

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
        //Question
        $mainQuestion = $this->getMainQuestion($payperiodid);
        $questionChecker = $this->getQuestionChecker($mainQuestion);
        $completeQuestion[] = $this->getCompleteQuestionArray($mainQuestion);
        //Client and Answer Query
        $clientDetailsMainAnswer = $this->getClientDetailsMainAnswer($payperiodid);
        //Client Details
        $clientDetails = $this->getClientArray($clientDetailsMainAnswer);
        //main answer
        $mainAnswerChecker = $this->getMainAnswer($clientDetailsMainAnswer);
        //Sub Answer

        $subQuestionChecker = $this->getSubQuestionChecker();
        $subAnswer = $this->subQuestionArray($payperiodid, $clientDetails);
        $subAnswerChecked = $this->subQuestionAnswerChecking($subQuestionChecker, $subAnswer, $clientDetails);
        //completeAnswer
        $completeMixedAnswer = $this->answerMixer($questionChecker, $clientDetails, $mainAnswerChecker,$subAnswerChecked);
        $toDisplay = [];
        $toDisplay['question'] = $completeQuestion;
        $toDisplay['answer'] = $completeMixedAnswer;
        return $toDisplay;
    }


    //Question Part starts
   public function getMainQuestion() {
        $mainQuestion = DB::table('template_forms')
                            ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                            ->where('parent_position', null)
                            ->where('template_forms.deleted_at', null)
                            ->where('template_questions_categories.deleted_at', null)
                            ->select('template_forms.id','multi_answer','position','question_category_id','description','question_text')
                            ->get();
        return $mainQuestion;
   }

   public function getQuestionChecker($mainQuestion) {
        $prevQuestionCatChecker = 1;
        $questionChecker;

        foreach($mainQuestion as $key=>$value) {
            $questCatCheck = $value->description;
            if ($prevQuestionCatChecker  != $questCatCheck) {
                $prevQuestionCatChecker = $questCatCheck;
                $i=0;
            }
            $questionChecker[$prevQuestionCatChecker][$i]['id']= $value->id;
            $questionChecker[$prevQuestionCatChecker][$i]['multi_answer']= $value->multi_answer;
            $questionChecker[$prevQuestionCatChecker][$i]['position']= $value->position;
            $questionChecker[$prevQuestionCatChecker][$i]['question_category_id']= $value->question_category_id;
            $questionChecker[$prevQuestionCatChecker][$i]['description']= $value->description;
            $questionChecker[$prevQuestionCatChecker][$i++]['question_text'] = $value->question_text;
        }
        return $questionChecker;
   }

   public function getCompleteQuestionArray($mainQuestion)  {
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
                $prevQuestionCategory = $questionCategory;
            }

            $subQueQuery = DB::table('template_forms')
                                        ->where('parent_position', $parentPosition)

                                        ->where('template_forms.deleted_at', null)
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
                    foreach ($multiQuesitonQuery as $key => $value) {
                        $answerType = $value->answer_type_id;
                        if ($value->answer_type_id == 4 ) {
                            $multiQuestionAdhoc[] = [$value->question_text,"Date of absenteeism","Hours Booked Off","Reason","Notes"];
                        } else {
                            $multiQuestion[] = $value->question_text;
                        }
                    }
                        if ($value->answer_type_id == 4) {
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
   //Question Part Ends Here

   //ClientDetailsMainAnswer Part Starts
   public function getClientDetailsMainAnswer($payperiod_id) {
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
                        ->where('customer_reports.deleted_at', null)
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
        $preClient = 1;
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
    $answerChecker;
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
        $answerChecker[$j][$preAnswerChecker][$i]['element_id'] = $value->element_id;
        $answerChecker[$j][$preAnswerChecker][$i]['answer'] = $value->answer;
        $answerChecker[$j][$preAnswerChecker][$i]['multi_answer'] = $value->multi_answer;
        $answerChecker[$j][$preAnswerChecker][$i]['position'] = $value->position;
        $answerChecker[$j][$preAnswerChecker][$i]['description'] = $value->description;
        $answerChecker[$j][$preAnswerChecker][$i++]['question_category_id'] = $value->question_category_id;

      }
      return $answerChecker;
   }

   public function subQuestionArray($payperiodid, $clientDetails) {


       $subAnswerArray = array();
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
                        ->where('template_questions_categories.deleted_at', null)
                        ->where('customer_reports.deleted_at', null)
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
        foreach ($subAnswer as $key => $value) {
            if($customer_id != $value->id) {
                $customer_id = $value->id;
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

   public function getSubQuestionChecker() {
    $subQuestionChecker = DB::table('template_forms')
                             ->join('template_questions_categories', 'template_forms.question_category_id', '=', 'template_questions_categories.id')
                             ->where('template_forms.parent_position','!=',null)
                             ->where('template_forms.deleted_at', null)
                             ->where('template_questions_categories.deleted_at', null)
                             ->select('template_forms.id','multi_answer','position','parent_position','question_category_id','description','question_text','answer_type_id')
                             ->get();

        $parent_position = 0;
        foreach ($subQuestionChecker as $key => $value) {
            if ($parent_position != $value->parent_position) {
                $parent_position = $value->parent_position;
                $m = 0;
            }

            $subQuestionCheckerArray[$parent_position][$value->id]['id'] = $value->id;
            $subQuestionCheckerArray[$parent_position][$value->id]['answer_type_id'] = $value->answer_type_id;
            $subQuestionCheckerArray[$parent_position][$value->id]['question_text'] = $value->question_text;

        }
        return $subQuestionCheckerArray;
    }

    public function subQuestionAnswerChecking($subQuestionCheckerArray,$subAnswerArray, $clientDetails) {
        foreach ($subQuestionCheckerArray as $k => $value) {
            foreach ($subAnswerArray as $key => $val) {
                if(!array_key_exists($k, $val)) {
                    $subAnswerArray[$key][$k]= $subQuestionCheckerArray[$k];

                    foreach ($subAnswerArray[$key][$k] as $innerAnswerKey => $content) {
                       $subAnswerArray[$key][$k][$innerAnswerKey]['answer'] = null;
                       $subAnswerArray[$key][$k][$innerAnswerKey]['element_id'] = 1;
                       unset($subAnswerArray[$key][$k][$innerAnswerKey]['id']);
                       unset($subAnswerArray[$key][$k][$innerAnswerKey]['question_text']);
                    }
                }

                // foreach ($value as $ky => $v) {
                //     foreach ($subAnswerArray[$key][$k] as $innerAnswerKey => $content) {
                //         if($content['element_id'] == $v['id']) {
                //             $subAnswerArray[$key][$k][$innerAnswerKey]['answer'] = null;
                //         }
                //     }
                // }
            }
        }

        // dd($subQuestionCheckerArray,$subAnswerArray);
        return $subAnswerArray;
    }

   public function answerMixer($questionChecker, $clientDetails, $mainAnswerChecker, $subAnswer) {
    $completeAnswerArray = [];
    for ($q = 0; $q <count($clientDetails); $q++) {
        $answerArray = [];
        //category checking in answer
        foreach ($questionChecker as $key => $value) {
            if(!array_key_exists("$key",$mainAnswerChecker[$q])) {
                $mainAnswerChecker[$q][$key]=[];
            }
        }

        //answer to question is available or not
        foreach($questionChecker as $key=>$value) {
            foreach($value as $k => $v) {
                if (!array_key_exists("$k",$mainAnswerChecker[$q][$key])) {
                    $mainAnswerChecker[$q][$key][$k]['element_id'] = $v['id'];
                    $mainAnswerChecker[$q][$key][$k]['answer'] = null;
                    $mainAnswerChecker[$q][$key][$k]['multi_answer'] = $v['multi_answer'];
                    $mainAnswerChecker[$q][$key][$k]['position'] = $v['position'];
                    $mainAnswerChecker[$q][$key][$k]['description'] = $v['description'];
                    $mainAnswerChecker[$q][$key][$k]['question_category_id'] = $v['question_category_id'];
                }
            }
        }

        //reduce answer categories to reduced question categories
        $answerCategoryCheck;
        foreach ($mainAnswerChecker[$q] as $key => $value) {
            if(array_key_exists("$key",$questionChecker)) {
                $answerCategoryCheck[$q][$key] = $mainAnswerChecker[$q][$key];
            }
        }

        $answerDoubleCheck;
        //reduce answer to question
        foreach ($answerCategoryCheck[$q] as $key => $value) {
            foreach ($value as $k => $v) {
                if(array_key_exists("$k",$questionChecker[$key])) {
                    if($questionChecker[$key][$k]['id'] == $mainAnswerChecker[$q][$key][$k]['element_id']) {
                        $answerDoubleCheck[$q][$key][$k]['answer'] = $v['answer'];
                        $answerDoubleCheck[$q][$key][$k]['multi_answer'] = $v['multi_answer'];
                        $answerDoubleCheck[$q][$key][$k]['position'] = $v['position'];
                        $answerDoubleCheck[$q][$key][$k]['description'] = $v['description'];
                        $answerDoubleCheck[$q][$key][$k]['question_category_id'] = $v['question_category_id'];
                    } else {
                        $answerDoubleCheck[$q][$key][$k]['answer'] = null;
                        $answerDoubleCheck[$q][$key][$k]['multi_answer'] = $v['multi_answer'];
                        $answerDoubleCheck[$q][$key][$k]['position'] = $v['position'];
                        $answerDoubleCheck[$q][$key][$k]['description'] = $v['description'];
                        $answerDoubleCheck[$q][$key][$k]['question_category_id'] = $v['question_category_id'];
                    }

                }
            }
        }
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

        foreach ($answerDoubleCheck[$q] as $key => $value) {
            foreach ($value as $k => $val) {
                $mainAnswerText = $val['answer'];
                $multiAnswerQuestion =  $val['multi_answer'];
                $parentQuestionPosition = $val['position'];
                $questionCategoryId = $val['question_category_id'];

                if ($prevAnswerCategoryname != $questionCategoryId) {
                    $answerArray[] = $val['description'];
                    $prevAnswerCategoryname = $questionCategoryId;
                }

                if ($multiAnswerQuestion === 0) {
                    $answerArray[] = $mainAnswerText;
                    if(!empty($subAnswer[$customer_id][$parentQuestionPosition])) {
                        foreach ($subAnswer[$customer_id][$parentQuestionPosition] as $key => $value) {
                            $answerArray[] = $value['answer'];
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

        $avegscore = $this->customer_map_repository->getPayperiodAvgReport($client_id, [$payperiod_id]);

        $answerArray[] = round($avegscore['score']['total'], 3);

        $completeAnswerArray[] = $answerArray;
    }//for
    return $completeAnswerArray;
   }

   public function getByPayperiodAndCustomer($inputs){
        return CustomerPayperiodTemplate::where('customer_id',$inputs['customer_id'])
        ->where('payperiod_id',$inputs['payperiod_id'])
        ->count();
   }

}

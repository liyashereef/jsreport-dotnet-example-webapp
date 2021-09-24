<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Reports\Repositories\CustomerPayPeriodTemplateRepository;

class CustomerSurveyReportController extends Controller
{   
    protected $pay_period_repository;
    protected $customer_pay_period_template_repository;

    public function __construct(
        PayPeriodRepository $pay_period_repository,
        CustomerPayPeriodTemplateRepository $customer_pay_period_template_repository
    ) {
        $this->pay_period_repository = $pay_period_repository;
        $this->customer_pay_period_template_repository = $customer_pay_period_template_repository;
    }
    

    public function surveryreport(){
        $pay_periods = $this->pay_period_repository->getAllActivePayPeriodsbelowdate();
        return view("reports::customersurveyreport.customer-survey-report",compact('pay_periods'));
    }

    public function getsurveryreport(Request $request){
        $payperiodid=$request->get('payperiodid');
        //repository function called
        $result = $this->customer_pay_period_template_repository->getCustomerSurveyReport($payperiodid);
        if ($result == 0) {
            //return "Pay Period Empty";
            return;
        } else if ($result == 1) {
            return "More and Two Templates were active during this period";
        } else {
            $completeQuestionArray = $result['question'][0];
            $completeAnswerArray = $result['answer'];
            return view("reports::customersurveyreport.partials.customer-survey-report",compact('completeQuestionArray','completeAnswerArray'));
        }
        
    }
}

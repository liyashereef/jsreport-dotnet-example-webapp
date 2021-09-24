<?php

namespace Modules\Supervisorpanel\Repositories;

use Auth;
use Carbon\Carbon;
use Mail;
use Modules\Supervisorpanel\Models\CustomerPayperiodTemplate;
use Illuminate\Database\Eloquent\Model;
use Modules\Supervisorpanel\Models\CustomerReportEmailScheduler;
use Modules\SupervisorPanel\Mail\SendSurveySubmitNotification;

class CustomerReportEmailSchedulerRepository
{

    public function __construct() 
    {
        
    }

    /**
     * Get Customer who submitted payperiod templates
     *
     * @param int payperiod id
     * @param Array  customer list
     *
     * @return array
     *
     */
    public function getCustomersubmittedtemplate($payperiodid,$customers_list)
    {
        $customerPayperiodTemplate = CustomerPayperiodTemplate::whereIn('customer_id', $customers_list)
                ->where('payperiod_id', $payperiodid)
                ->get()
                ->pluck('customer_id')
                ->toArray();

        return $customerPayperiodTemplate;
    }

    /**
     * Get Customer who submitted payperiod templates
     *
     * @param int payperiod id
     * @param int  customer id
     * @param date todays date
     * 
     * @return void
     *
     */
    public function removeFromScheduledemail($payperiodid,$customer,$today){
        $customerarray = CustomerReportEmailScheduler::where(["customerid"=>$customer,"payperiodid"=>$payperiodid,"sendflag"=>0]);
        $customerarray->delete();
    }

    /**
     * Create a database for those who haven't submitted database
     *
     * @param int  customer id
     * @param array Supervisor array
     * @param date todays date
     * @param date Payperiod date
     * @param int  Payperiod id
     * 
     * @return void
     *
     */
    public function setCustomernotsubmitteddb($customer_id,$supervisorarray,$today,$payperiodstartdate,$payperiodid)
    {
        $supervisoremailidarray = $supervisorarray->pluck('email')->toArray();
        $emailid = implode (",",$supervisoremailidarray);
        $maildate = $today;
        $cronflag = CustomerReportEmailScheduler::where(["customerid"=>$customer_id,"payperiodid"=>$payperiodid])->count();

        if($cronflag == 0)
        {
            for($i=0;$i<3;$i++)
            {
                if($i == 0)
                {
                    $maildate = $today;
                }
                else
                {
                    $maildate = date("Y-m-d",strtotime($maildate."+2 days"));
                }
                if($emailid!="")
                {
                    $emailidarray = explode(",",$emailid);
                    foreach ($emailidarray as $value) {
                        $Mailscheduler = new CustomerReportEmailScheduler;
                        $Mailscheduler->customerid = $customer_id;
                        $Mailscheduler->payperiodid = $payperiodid;
                        $Mailscheduler->payperioddate = $payperiodstartdate;
                        $Mailscheduler->supervisormail = $value;
                        $Mailscheduler->maildate = $maildate;
                        $Mailscheduler->save();
                    }
                    
                }               
            }
        }
    }

    /**
     * Send scheduled mail with template
     *
     * @param date todays date
     * 
     * @return void
     *
     */
    public function sendScheduledemail($today)
    {
        $mailscheduled = CustomerReportEmailScheduler::where(["maildate"=>$today,"sendflag"=>0])->get();
        $mailarray = array();
        foreach ($mailscheduled as $mailkey => $mailvalue) {
            $cron_id = $mailvalue->id;
            $customer_id = $mailvalue->customerid;
            $payperiod_id = $mailvalue->payperiodid;
            $payperiod_date = $mailvalue->payperioddate;
            $supervisor_mailarray = explode(",",$mailvalue->supervisormail);
            $mail_date = $mailvalue->maildate;
            
            foreach ($supervisor_mailarray as $supervisor_mail) {
                # code...
                $flag = $this->sendNotification($supervisor_mail);
                if($flag=="true")
                {
                    $crtable = CustomerReportEmailScheduler::find($cron_id);
                    $crtable->sendflag =1;
                    $crtable->save();
                }
            }
        }
        
    }
    
    /**
     * To send notificaion to supervisors
     *
     * @param $survey_not_submitted_supervisors
     * @return boolean string
     */
    public function sendNotification($survey_not_submitted_supervisors)
    {
        $when = Carbon::now()->addMinutes(10);

        try {         
            Mail::to($survey_not_submitted_supervisors)
                ->queue(new SendSurveySubmitNotification('mail.survey-submit-notification.supervisorremindermail'));
            return "true";           
        } catch (Throwable $th) {
            //throw $th;
            return "false";
        }
       
    }

}

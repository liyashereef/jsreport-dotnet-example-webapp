<?php

namespace Modules\Generator\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\Employee;
use Modules\Employeescheduling\Models\EmployeeSchedule;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog;
use Modules\Generator\Jobs\MakeNextSchedule;
use Modules\Employeescheduling\Repositories\InheritScheduleRepository;


class CreateCurrentYearSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userschedule:create  {pay_periodid?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create schedule under given Date Range.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $payperiodrepository,$inheritScheduleRepository;

    public function __construct(PayPeriodRepository $pay_period_repository,InheritScheduleRepository $inheritScheduleRepository)
    {
        parent::__construct();
        $this->payperiodrepository = $pay_period_repository;
        $this->inheritScheduleRepository = $inheritScheduleRepository;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $payPeriodId= PayPeriod::select('id', 'year', 'pay_period_name', 'short_name', 'start_date', 'end_date')
        ->where('start_date', '>', today())
        ->take(1)
        ->get();        
        

        $arguments = $this->arguments();
        if (!empty($arguments['pay_periodid']) ) {
            $payPeriodId = $this->option('pay_periodid');
        }
        try {
            $customers=Customer::with(['customerEmployeeAllocation','employeeSchedule'])
        ->whereHas('employeeSchedule',function($q)use($payPeriodId){
        })
        ->whereHas("customerEmployeeAllocation")->whereActive(true)->get();
        $i=0;
        foreach ($customers as $customer) {
            $customerId=$customer->id;
            
            
            $empSchedules= EmployeeSchedule::where("created_at","<=",date("Y-m-d"))
            ->where("customer_id",$customerId)->orderBy("created_at","desc")->first();
            
            if($empSchedules!=null  && $empSchedules->scheduleTimeLogs->count()>0){
                $cheduleId=$empSchedules->id;
                try {
                    $sourcePayperiods=$empSchedules->scheduleTimeLogs[0]->payperiod->id;
                } catch (\Throwable $th) {
                    //throw $th;
                    dd($empSchedules->scheduleTimeLogs->count());
                }
                
                $scheduleCount=EmployeeScheduleTimeLog::where("payperiod_id",$sourcePayperiods)->whereHas("schedule",function($q)use($customerId)
                {
                    return $q->where("customer_id",$customerId);
                })->count();
                if($scheduleCount>0){
                    if($sourcePayperiods>0){
                        try {
                            $i++;
                            if($i==8)
                            {
                            }
                            // if($customerId==35 && $sourcePayperiods==69){
                            //     $sched=EmployeeSchedule::where("customer_id",35)
                            //     ->whereHas("scheduleTimeLogs",function($q){
                            //         return $q->whereIn("payperiod_id",[69]);
                            //     })->first();
                            //     dd($sched);
                            // }
 
                       
                            MakeNextSchedule::Dispatch($customer,$sourcePayperiods,$payPeriodId,$this->inheritScheduleRepository);
                        } catch (\Throwable $th) {
                            throw $th;
                            //dd($customer,$sourcePayperiods,$payPeriodId);
                        }
                    // echo $customerId;

                }
                }else{
                    
                }
                
            }
            
        }
        } catch (\Throwable $th) {
            dd($th);
        }
        
    }

    
}

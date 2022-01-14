<?php

namespace Modules\Generator\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Employeescheduling\Repositories\InheritScheduleRepository;

class MakeNextSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $customer,$payPeriodId,$sourcePayperiods,$inheritScheduleRepository;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer,$sourcePayperiods,$payPeriodId,$inheritScheduleRepository)
    {
        $this->customer=$customer;
        $this->sourcePayperiods=$sourcePayperiods;
        $this->payPeriodId=$payPeriodId->first();
        $this->inheritScheduleRepository = $inheritScheduleRepository;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $startDate=$this->payPeriodId->start_date;
        $endDate=$this->payPeriodId->end_date;
        $this->inheritScheduleRepository->inheritProcess($this->customer->id,($this->sourcePayperiods),$this->payPeriodId->id);

        // $employeeAllocation=($this->customer->customerEmployeeAllocation);
        // if($employeeAllocation!=null)
        // foreach ($employeeAllocation as $allocation) {
        //     $userId=$allocation->user_id;
        //     $start = \Carbon::createFromFormat('Y-m-d', substr($startDate, 0, 10));
        //     $end = \Carbon::createFromFormat('Y-m-d', substr($endDate, 0, 10));
    
        //     $dates = [];
    
        //     while ($start->lte($end)) {
    
        //         $dates[] = $start->copy()->format('Y-m-d');
    
        //         $start->addDay();
        //     }
        //     dd($dates);
             
        //     # code...
        // }
    }
}

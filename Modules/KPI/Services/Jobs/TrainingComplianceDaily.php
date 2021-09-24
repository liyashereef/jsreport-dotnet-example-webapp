<?php

namespace Modules\KPI\Services\Jobs;

use Modules\Admin\Models\User;
use Carbon\Carbon;
use Modules\Admin\Models\Customer;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;

class TrainingComplianceDaily extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;
    protected $trainingUserCourseAllocationRepository;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
        $this->trainingUserCourseAllocationRepository = app()->make(TrainingUserCourseAllocationRepository::class);
    }

    public function run()
    {
        $datas = [];
        $request = [];

        $customerLists = Customer::select('id', 'project_number', 'client_name')
            ->has('customerEmployeeAllocation')
            ->with(['customerEmployeeAllocation' => function ($query) {
                return $query->select('id', 'user_id', 'customer_id');
            }])
            ->get();

        foreach ($customerLists as $customer) {
            $request['customer_id'] = $customer->id;
            $customerAllocatedUserId = data_get($customer, 'customerEmployeeAllocation.*.user_id');
            $request['user_ids'] = data_get(User::whereIn('id', $customerAllocatedUserId)->get(), '*.id');
            $trainingData = $this->trainingUserCourseAllocationRepository->getKpiData($request);
            $avg = floatval($trainingData['percentage']);
            if ($avg > 0) {
                $datas[] = [
                    "kpid" => $this->options->kpi->id,
                    "customer_id" => $customer->id,
                    "process_date" => Carbon::parse($this->options->yesterday),
                    "value" => $avg,
                    "value_total" => $trainingData['total'],
                    "value_output" => $trainingData['completed']
                ];
            }
        }
        return $datas;
    }
}

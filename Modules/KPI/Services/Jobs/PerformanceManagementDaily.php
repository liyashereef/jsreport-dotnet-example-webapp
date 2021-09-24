<?php

namespace Modules\KPI\Services\Jobs;

use Modules\Admin\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\Employee;
use Modules\Client\Models\ClientEmployeeFeedback;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;

class PerformanceManagementDaily extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
    }

    public function run()
    {
        $datas = [];
        $customerLists = Customer::select('id', 'project_number', 'client_name')
            ->has('customerEmployeeAllocation')
            ->with(['customerEmployeeAllocation' => function ($query) {
                return $query->select('id', 'user_id', 'customer_id');
            }])->get();

        foreach ($customerLists as $customer) {
            $customerAllocatedUserId = collect($customer->customerEmployeeAllocation)->pluck('user_id');
            // $customerAllocatedUserId = data_get($customer, 'customerEmployeeAllocation.*.user_id');
            // $userIds = data_get(User::whereIn('id', $customerAllocatedUserId)->get(), '*.id');
            $users = User::whereIn('id', $customerAllocatedUserId)->get();
            // $managerRatingAvg = Employee::whereIn('user_id', $userIds)->average('employee_rating');

            // $clientRatings = $this->clientEmployeeFeedback->whereIn('user_id', $userIds)->has('userRating')
            //     ->groupBy('employee_rating_lookup_id')
            //     ->join('employee_rating_lookups', 'employee_rating_lookups.id', '=', 'client_employee_feedbacks.employee_rating_lookup_id')
            //     ->select('employee_rating_lookup_id', \DB::raw('avg(employee_rating_lookups.score) AS average'))
            //     ->get();
            $arrayAverage = [];
            foreach ($users as $key => $user) {
                $managerRatingAvg = Employee::where('user_id', $user->id)->average('employee_rating');

                $clientRatings = ClientEmployeeFeedback::where('user_id', $user->id)->has('userRating')
                    ->groupBy('employee_rating_lookup_id')
                    ->join('employee_rating_lookups', 'employee_rating_lookups.id', '=', 'client_employee_feedbacks.employee_rating_lookup_id')
                    ->select('employee_rating_lookup_id', DB::raw('avg(employee_rating_lookups.score) AS average'))
                    ->get();
                $clientRatingAvg = $clientRatings->avg('average');

                $managerRatingAvg = empty($managerRatingAvg) ? 0 : $managerRatingAvg;
                $clientRatingAvg = empty($clientRatingAvg) ? 0 : $clientRatingAvg;

                $totalAvg = 0;
                if ($managerRatingAvg != 0 && $clientRatingAvg != 0) {
                    $totalAvg = floatval(($managerRatingAvg + $clientRatingAvg) / 2);
                } else {
                    if ($managerRatingAvg != 0) {
                        $totalAvg = floatval($managerRatingAvg);
                    }
                    if ($clientRatingAvg != 0) {
                        $totalAvg = floatval($totalAvg);
                    }
                }
                if ($totalAvg > 0) {
                    array_push($arrayAverage, $totalAvg);
                }

                // $details[$key]['userId'] = $userId;
                // $details[$key]['name'] =$user->first_name.' '.$user->last_name;
                // $details[$key]['manager_rating'] = $managerRatingAvg;
                // $details[$key]['client_rating'] = $clientRatingAvg;
                // $details[$key]['average_rating'] =  $totalAvg;

            }

            if (sizeof($arrayAverage) > 0) {
                $avgRating = array_sum($arrayAverage) / sizeof($arrayAverage);
                if ($avgRating > 0) {
                    $datas[] = [
                        "kpid" => $this->options->kpi->id,
                        "customer_id" => $customer->id,
                        "process_date" => Carbon::parse($this->options->yesterday),
                        "value" => $avgRating,
                        "value_total" => '',
                        "value_output" => ''
                    ];
                }
            }
        }
        return $datas;
    }
}

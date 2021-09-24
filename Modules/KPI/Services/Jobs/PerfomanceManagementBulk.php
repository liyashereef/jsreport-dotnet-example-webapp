<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Client\Models\ClientEmployeeFeedback;
use Modules\Hranalytics\Models\UserRating;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;

class PerfomanceManagementBulk extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
    }

    public function run()
    {
        $datas = [];
        $arguments = $this->options->arguments;
        $to = $this->options->today;
        if (isset($arguments['start_date']) && isset($arguments['end_date'])) {
            $from = Carbon::parse($arguments['start_date'])->startOfDay();
            $to = Carbon::parse($arguments['end_date'])->endOfDay();
        }

        //Get client rating of date range
        $clientRatingQue = ClientEmployeeFeedback::select('id as cef_id', 'user_id', 'customer_id', DB::raw('DATE(created_at) as createdAt'));
        if (isset($from) && isset($to)) {
            $clientRatingQue->whereBetween('created_at', [$from, $to]);
        } else {
            $clientRatingQue->where('created_at', '<=', $to);
        }
        if (isset($arguments['customer_id'])) {
            $clientRatingQue->where('customer_id', $arguments['customer_id'])
                ->orWhere(function ($query) {
                    return $query->whereNull('customer_id');
                });
        }
        $clientRatings = $clientRatingQue->get();

        //Get manager rating of date range
        $managerRatingQue = UserRating::select('id as ur_id', 'employee_id as user_id', 'customer_id', DB::raw('DATE(created_at) as createdAt'));
        if (isset($from) && isset($to)) {
            $managerRatingQue->whereBetween('created_at', [$from, $to]);
        } else {
            $managerRatingQue->where('created_at', '<=', $to);
        }
        if (isset($arguments['customer_id'])) {
            $managerRatingQue->where('customer_id', $arguments['customer_id'])
                ->orWhere(function ($query) {
                    return $query->whereNull('customer_id');
                });
        }
        $managerRatings = $managerRatingQue->get();

        // $managerRatingDates = $clientRatings->pluck('createdAt')->toArray();
        // $clientRatingDates =  $managerRatings->pluck('createdAt')->toArray();

        // $dates = array_unique(array_merge($managerRatingDates,$clientRatingDates));
        // sort($dates);

        $ratings =  array_merge($managerRatings->toArray(), $clientRatings->toArray());
        $uniqueEntryMap = [];

        foreach ($ratings as $rKey => $rating) {
            $cIds = [];
            if (!isset($arguments['customer_id'])) {
                $q = CustomerEmployeeAllocation::withTrashed();

                $dt = Carbon::parse($rating['createdAt']);
                $start = $dt->startOfDay();
                $end = (clone $dt)->endOfDay();

                //Get allocated customers
                $q->where('user_id', $rating['user_id']);
                $q->whereDate('from', '<=', $end);
                $q->where(function ($query) use ($start) {
                    $query->whereDate('to', '>=', $start)
                        ->orWhereNull('to');
                });

                $cIds = $q->get()->pluck('customer_id')->toArray();
                $cIds = array_unique($cIds);
            } else {
                $cIds = [$arguments['customer_id']];
            }

            $uIds = [];
            if (!empty($cIds)) {
                //Get employees of allocated customer
                $cq =  CustomerEmployeeAllocation::withTrashed();
                $cq->whereIn('customer_id', $cIds);
                $cq->whereDate('from', '<=', $end);
                $cq->where(function ($query) use ($start) {
                    $query->whereDate('to', '>=', $start)
                        ->orWhereNull('to');
                });
                $customerEmpAllObj = $cq->get();

                if (!empty($customerEmpAllObj)) {
                    foreach ($cIds as $key => $cId) {
                        $uniqueEntryKey = $cId . Carbon::parse($rating['createdAt'])->format('Y-m-d');

                        if (in_array($uniqueEntryKey, $uniqueEntryMap)) {
                            continue;
                        }
                        array_push($uniqueEntryMap, $uniqueEntryKey);

                        $arrayAverage = [];
                        $userSpitUp = [];
                        $uIds = $customerEmpAllObj->where('customer_id', $cId)->pluck('user_id')->toArray();

                        foreach ($uIds as $uKey => $uId) {
                            //Get client rating of date range
                            $clientRatingAvg = ClientEmployeeFeedback::where('user_id', $uId)
                                // ->has('userRating')
                                ->whereDate('client_employee_feedbacks.created_at', '<=', $end)
                                ->groupBy('employee_rating_lookup_id')
                                ->join('employee_rating_lookups', 'employee_rating_lookups.id', '=', 'client_employee_feedbacks.employee_rating_lookup_id')
                                ->select('employee_rating_lookup_id', DB::raw('avg(employee_rating_lookups.score) AS average'))
                                ->get();

                            //Get manager rating of date range
                            $managerRatingAvg = UserRating::where('employee_id', $uId)
                                ->where('created_at', '<=', $end)
                                ->groupBy('employee_id')
                                ->select('employee_id', DB::raw('avg(rating) AS average'))
                                ->get();

                            $managerRatingAvg = empty($managerRatingAvg) ? 0 : $managerRatingAvg->avg('average');
                            $clientRatingAvg = empty($clientRatingAvg) ? 0 : $clientRatingAvg->avg('average');

                            $totalAvg = 0;
                            if ($managerRatingAvg != 0 && $clientRatingAvg != 0) {
                                $totalAvg = floatval(($managerRatingAvg + $clientRatingAvg) / 2);
                            } else {
                                if ($managerRatingAvg != 0) {
                                    $totalAvg = floatval($managerRatingAvg);
                                } else if ($clientRatingAvg != 0) {
                                    $totalAvg = floatval($totalAvg);
                                }
                            }
                            if ($totalAvg > 0) {
                                array_push($arrayAverage, $totalAvg);

                                $userSpitUp[$uKey]['userId'] = $uId;
                                // $details[$uKey]['name'] =$user->first_name.' '.$user->last_name;
                                $userSpitUp[$uKey]['manager_rating'] = $managerRatingAvg;
                                $userSpitUp[$uKey]['client_rating'] = $clientRatingAvg;
                                $userSpitUp[$uKey]['average_rating'] =  $totalAvg;
                            }
                        }
                        if (sizeof($arrayAverage) > 0) {
                            $avgRating = array_sum($arrayAverage) / count($arrayAverage);
                            if ($avgRating > 0) {
                                $datas[] = [
                                    "kpid" => $this->options->kpi->id,
                                    "customer_id" => $cId,
                                    "process_date" => Carbon::parse($rating['createdAt']),
                                    "value" => $avgRating,
                                    "split_up" => $userSpitUp,
                                    "value_total" => '',
                                    "value_output" => ''
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $datas;
    }
}

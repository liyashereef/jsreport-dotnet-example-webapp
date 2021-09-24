<?php

namespace Modules\KPI\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\KPI\Models\KpiData;

class KpiMonthlyRepository
{
    /**
     * processAll [true] -> calculate monthly data from beginning
     * processAll [false] -> calculate  curret month data
     */
    public function runMonthlyJob($processAll = false)
    {
        $date = Carbon::now()->subDay(1);

        //Current Month Process
        if ($processAll == false) {

            $datas = KpiData::whereYear('process_date', '=', $date->year)
                ->whereMonth('process_date', '=', $date->month)
                ->groupBy('customer_id')
                ->groupBy('kpid')
                ->select('customer_id', 'kpid', DB::raw('round(AVG(value),0) as value'))
                ->get();
        }
    }
}

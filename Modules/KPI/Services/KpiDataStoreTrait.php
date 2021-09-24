<?php

namespace Modules\KPI\Services;

use Carbon\Carbon;
use DateTime;
use Modules\KPI\Models\KpiData;

trait KpiDataStoreTrait
{
    /**
     * @param array $results
     * @param bool bulkMode
     * @param KpiJobInterface $job
     */
    public function storeToDB(array $results, bool $bulkMode, KpiJobInterface $job)
    {
        $jOpt = $job->getJobOptions();
        if ($bulkMode) {
            //remove all data of curresponding kpi
            KpiData::where('kpid', $jOpt->kpi->id)->delete();

            foreach ($results as $res) {
                $processDate = $res['process_date']->toDateTimeString();
                $res['process_date'] = new \MongoDB\BSON\UTCDateTime(new \DateTime($processDate));
                //Store data
                KpiData::create($res);
            }
        } else { //Daily mode
            foreach ($results as $res) {

                $startDateTime = Carbon::parse($res['process_date'])->startOfDay();
                $endDateTime = Carbon::parse($res['process_date'])->endOfDay();
                if($jOpt->kpi->id != 2){
                    $q =  KpiData::where('kpid', $res['kpid'])
                    ->where('customer_id', $res['customer_id'])
                    ->where('process_date', '>=', $startDateTime)
                    ->where('process_date', '<=', $endDateTime);
                }else{
                    $q =  KpiData::where('kpid', $res['kpid'])
                    ->where('customer_id', $res['customer_id'])
                    ->where('process_date', '>=', $startDateTime)
                    ->where('process_date', '<=', $endDateTime)
                    ->where('payperiod_id', '=', $res['payperiod_id']);
                }


                //Apply custom filter
                // if ($job instanceof KpiJobInterface) {  //TODO: enable in case of custom filter
                //     $q =  $job->getDbUpdateFilter($q, $res);
                // }

                $kpi = $q->first();
                $processDate = $res['process_date']->toDateTimeString();
                $res['process_date'] = new \MongoDB\BSON\UTCDateTime(new \DateTime($processDate));
                if (empty($kpi)) {
                    // $data['created_at'] = Carbon::now();
                    KpiData::create($res);
                } else {
                    KpiData::where('_id', $kpi->_id)->update($res);
                }
            }
        }
    }
}

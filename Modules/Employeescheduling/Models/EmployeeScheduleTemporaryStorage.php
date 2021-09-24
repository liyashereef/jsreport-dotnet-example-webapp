<?php

namespace Modules\Employeescheduling\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeScheduleTemporaryStorage extends Model
{
    protected $fillable = ['scheduleid', 'customer_id', 'payperiod', 'employeeid', 'week', 'hours', 'scheduledate', 'starttime', 'endtime', 'created_by', 'overlaps'];

    public function insertData($scheduleid, $customer_id, $payperiod, $employeeid, $week, $hours, $scheduledate, $starttime, $endtime, $created_by, $overlaps)
    {
        return $this->updateOrCreate(
            [
                'scheduleid' => $scheduleid,
                'customer_id' => $customer_id,
                'employeeid' => $employeeid,
                'scheduledate' => $scheduledate,
            ],
            [
                'scheduleid' => $scheduleid,
                'customer_id' => $customer_id,
                'scheduledate' => $scheduledate,
                'payperiod' => $payperiod,
                'employeeid' => $employeeid,
                'week' => $week,
                'hours' => $hours,
                'starttime' => $starttime,
                'endtime' => $endtime,
                'created_by' => $created_by,
                'overlaps' => $overlaps,
            ]
        );
    }
}

<?php

namespace Modules\KPI\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class KpiMonthlyData extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'kpi_monthly_datas';

    protected $dates = ['created_at','process_date'];

    protected $fillable = [
        'kpid',
        'customer_id',
        'value',
        'created_at',
        'process_date',
        'value_total',
        'value_output',
    ];

    public function user()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function kpidMaster()
    {
        return $this->belongsTo(KpiMaster::class, 'kpid');
    }
}

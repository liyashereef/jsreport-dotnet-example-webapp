<?php

namespace Modules\KPI\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class KpiData extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'kpi_data';

    protected $dates = ['process_date'];

    protected $fillable = [
        'kpid',
        'customer_id',
        'payperiod_id', 
        'template_id',
        'alloction_template_id',
        'value',
        'value_total',
        'value_output',
        'is_submitted',
        'split_up',
        'created_at',
        'process_date'
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

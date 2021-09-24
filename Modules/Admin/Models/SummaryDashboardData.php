<?php

namespace Modules\Admin\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class SummaryDashboardData extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'summary_dashboard_data_entries';


    protected $fillable = [
        'sd_id',
        'customer_id',
        'value',
        'created_at',
        'process_date',
        'category_id',
        'payperiod_id',
        'user_id',
        'is_manual_entry',
    ];
    protected $dates = ['created_at', 'process_date'];

    public function user()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function kpidMaster()
    {
        return $this->belongsTo(summaryDashboardMaster::class, 'kpid');
    }
}

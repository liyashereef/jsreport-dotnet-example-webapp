<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiMasterCustomerAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_id', 'kpi_master_id', 'is_active', 'created_by', 'updated_by'];

    public function kpiMaster()
    {
        return $this->belongsTo(KpiMaster::class, 'kpi_master_id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}

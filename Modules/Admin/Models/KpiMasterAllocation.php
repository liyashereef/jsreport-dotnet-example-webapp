<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiMasterAllocation extends Model
{
    use SoftDeletes;

    protected $fillable = ['kpi_customer_header_id', 'kpi_master_id', 'is_active', 'created_by', 'updated_by'];

    public function kpiMaster()
    {
        return $this->belongsTo(KpiMaster::class, 'kpi_master_id')->withTrashed();
    }

    public function kpiCustomerHeader()
    {
        return $this->belongsTo(KpiCustomerHeader::class, 'kpi_customer_header_id');
    }

    public function kpiThresholds()
    {
        return $this->hasMany(KpiMasterThreshold::class, 'kpi_master_allocation_id');
    }

}

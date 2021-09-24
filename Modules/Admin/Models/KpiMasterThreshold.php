<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiMasterThreshold extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kpi_master_allocation_id', 'kpi_master_id',
        'kpi_threshold_color_id', 'min', 'max', 'is_active', 'created_by', 'updated_by'
    ];
    protected $appends = ['is_percentage','max_val_fmt','min_val_fmt'];

    public function KpiMasterAllocation()
    {
        return $this->belongsTo('Modules\Admin\Models\KpiMasterAllocation', 'kpi_master_allocation_id');
    }

    public function KpiMaster()
    {
        return $this->belongsTo('Modules\Admin\Models\KpiMasterAllocation', 'kpi_master_id');
    }

    public function kpiThresholdColor()
    {
        return $this->belongsTo(KpiThresholdColor::class, 'kpi_threshold_color_id');
    }

    public function getIsPercentageAttribute(): bool
    {
        return ($this->KpiMasterAllocation->kpiMaster->threshold_type == 2) ? true : false;
    }

    public function getMaxValFmtAttribute()
    {
        return floatval($this->max);
    }

    public function getMinValFmtAttribute()
    {
        return floatval($this->min);
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiMaster extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','machine_name','threshold_type','is_active','created_by','updated_by'];

    public function kpiMasterAllocation(){
        return $this->hasMany('Modules\Admin\Models\KpiMasterAllocation', 'kpi_master_id','id');
    }

    public function KpiMasterCustomerAllocation(){
        return $this->hasMany('Modules\Admin\Models\KpiMasterCustomerAllocation', 'kpi_master_id','id');
    }


}


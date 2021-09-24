<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiCustomerHeader extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','is_active','created_by','updated_by'];

    public function kpiMasterAllocation(){
        return $this->hasMany(KpiMasterAllocation::class,'kpi_customer_header_id');
    }
}


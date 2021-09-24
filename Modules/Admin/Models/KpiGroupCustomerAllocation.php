<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiGroupCustomerAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_id', 'kpi_group_id', 'created_by', 'updated_by'];

    public function group()
    {
        return $this->belongsTo(KpiGroup::class, 'kpi_group_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function kpiMasterCustomerAllocation(){
        return $this->hasMany(KpiMasterCustomerAllocation::class, 'customer_id', 'customer_id');
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class KpiGroup extends Model
{
    protected $fillable = ['name', 'is_active', 'parent_id', 'created_by', 'updated_by'];

    public function parent()
    {
        return $this->belongsTo(KpiGroup::class, 'parent_id');
    }
    public function parents()
    {
        return $this->belongsTo(KpiGroup::class, 'parent_id')
        ->with(['parents'=>function($que) {
            return $que->select('id','name','parent_id');
        }]
        );
    }
    public function isRootNode()
    {
        return $this->parent_id == null ? true : false;
    }

     //Get all parent nested childs
     public function family()
     {
         return $this->hasMany(KpiGroup::class,'parent_id')
         ->select('id','name','parent_id')
         ->with(['family'=>function($que) {
             return $que->select('id','name','parent_id');
         },'family.kpiGroupCustomers'=>function($que) {
            return $que->select('id','customer_id','kpi_group_id');
        },'family.kpiGroupCustomers.kpiMasterCustomerAllocation'=>function($que) {
            return $que->select('id','customer_id','kpi_master_id');
        },
        'kpiGroupCustomers'=>function($que) {
            return $que->select('id','customer_id','kpi_group_id');
        },'kpiGroupCustomers.kpiMasterCustomerAllocation'=>function($que) {
            return $que->select('id','customer_id','kpi_master_id');
        }
        ]);
     }

    //Get all parent childs
    public function children()
    {
        return $this->hasMany(KpiGroup::class, 'parent_id');
    }

    public function kpiGroupCustomers(){
        return $this->hasMany(KpiGroupCustomerAllocation::class, 'kpi_group_id');
    }
}

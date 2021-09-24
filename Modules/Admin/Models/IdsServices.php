<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsServices extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'short_name', 'rate','tax_master_id', 'description','is_photo_service','is_photo_service_required'];

    public function IdsOfficeServiceAllocation()
    {
        return $this->hasMany('Modules\Admin\Models\IdsOfficeServiceAllocation', 'ids_service_id', 'id');
    }

     public function taxMaster()
    {
        return $this->belongsTo('Modules\Expense\Models\ExpenseTaxMaster','tax_master_id');
    }
}

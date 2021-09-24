<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseTaxMasterLog extends Model
{
    use SoftDeletes;
    protected $fillable = ['tax_master_id','archived_by','tax_percentage','effective_from_date','effective_end_date','status'];

    public function taxMaster()
    {
        return $this->belongsTo(ExpenseTaxMaster::class,'tax_master_id')->withTrashed();
    }
}

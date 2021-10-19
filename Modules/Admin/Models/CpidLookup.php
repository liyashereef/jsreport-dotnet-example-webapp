<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CpidLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['cpid','position_id', 'short_name', 'description', 'noc','cpid_function_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

     //get latest active date include feature date. eg effective from tomorrow
     public function cpid_rates_trashed()
     {
        return $this->hasOne('Modules\Admin\Models\CpidRates', 'cp_id', 'id')
            ->orderBy('id', 'desc')
            ->withTrashed();
 
     }

    //get latest active date include feature date. eg effective from tomorrow
    public function cpidRates()
    {
        return $this->hasOne('Modules\Admin\Models\CpidRates', 'cp_id', 'id');

    }

    //get effective date not cosidering feature date. latest date effective <=current date
    public function effectiveDate()
    {
        return $this->hasOne('Modules\Admin\Models\CpidRates', 'cp_id', 'id')
        ->whereDate('effective_from', '<=', Carbon::now())
        ->orderBy('effective_from','DESC')
        ->orderBy('created_at','DESC')
        ->withTrashed();

    }
    
    public function position()
    {
       return $this->belongsTo(PositionLookup::class)->withTrashed();
    }

    public function cpidFunction(){
        return $this->hasOne(CpidFunction::class,'id','cpid_function_id');
    }

    public function cpidCustomerAllocation()
    {
        return $this->hasMany('Modules\Admin\Models\CpidCustomerAllocations', 'cpid', 'id');
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CpidCustomerAllocations extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['customer_id','cpid','created_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

   
    
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function cpid_lookup()
    {
        return $this->belongsTo(CpidLookup::class,'cpid','id')->with('cpidRates','position');
    }

}

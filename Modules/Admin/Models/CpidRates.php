<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CpidRates extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['cp_id','effective_from','p_standard','p_overtime','p_holiday','b_standard','b_overtime','b_holiday'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    public function cpidLookup()
    {
        return $this->belongsTo(CpidLookup::class, 'cp_id');

    }
}

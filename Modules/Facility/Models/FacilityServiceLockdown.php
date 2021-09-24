<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityServiceLockdown extends Model
{
    use SoftDeletes;
    protected $fillable = ['model_id','model_type','start_date','start_time','end_date','end_time','created_by','updated_by'];
    
    public function model()
    {
        return $this->morphTo();
    }
}

<?php

namespace Modules\Facility\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FacilityServiceSlot extends Model
{
    use SoftDeletes;
    protected $fillable = ['model_type','model_id','slot_interval','start_date','expiry_date','created_by','updated_by'];
    /**
     * Get the owning imageable model.
     */
    public function model()
    {
        return $this->morphTo();
    }
}

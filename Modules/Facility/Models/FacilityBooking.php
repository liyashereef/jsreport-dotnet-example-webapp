<?php

namespace Modules\Facility\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FacilityBooking extends Model
{
    protected $table="facility_bookings";
    use SoftDeletes;
    protected $fillable = ['model_type','model_id','facility_user_id','booking_date_start','booking_date_end','updated_by','deleted_by'];

    /**
     * Get the owning imageable model.
     */
    public function model()
    {
        return $this->morphTo();
    }

    public function facilityUser(){
        return $this->belongsTo('Modules\Facility\Models\FacilityUser','facility_user_id','id');
    }

    public function facilitybooking(){
        
        return $this->belongsTo('Modules\Facility\Models\Facility','model_id','id');
    }

    public function facilityservicebooking(){
        return $this->belongsTo('Modules\Facility\Models\FacilityService','model_id','id');
    }

}

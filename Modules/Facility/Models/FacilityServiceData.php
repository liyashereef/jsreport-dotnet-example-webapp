<?php

namespace Modules\Facility\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FacilityServiceData extends Model
{
    protected $table="facility_service_data";
    use SoftDeletes;
    protected $fillable = ['model_type','model_id','weekend_booking','maxbooking_perday','tolerance_perslot','booking_window','start_date','expiry_date','created_by','updated_by'];

    /**
     * Get the owning imageable model.
     */
    public function model()
    {
        return $this->morphTo();
    }
}

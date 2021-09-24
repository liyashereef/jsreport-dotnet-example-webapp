<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsOffice extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'adress', 'latitude', 'longitude', 'phone_number', 'phone_number_ext', 'phone_number_ext',
        'office_hours_start_time', 'office_hours_end_time', 'special_instructions', 'intervals', 'interval_valid_date', 'icon_color_code','is_photo_service'];

    /**
     * Get project name and number
     *
     * @return void
     */
    public function getOfficeNameAndAddressAttribute()
    {
        return $this->name . ' - ' . $this->adress;
    }

    public function IdsOfficeTimings()
    {
        return $this->hasMany('Modules\Admin\Models\IdsOfficeTimings', 'ids_office_id', 'id');
    }

}

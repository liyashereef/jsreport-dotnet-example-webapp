<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsOfficeTimings extends Model
{
    use SoftDeletes;
    protected $fillable = ['ids_office_id', 'start_time', 'end_time', 'start_date','expiry_date',
                         'intervals', 'created_by', 'updated_by','lunch_start_time','lunch_end_time'];

    public function IdsOfficeSlots()
    {
        return $this->hasMany('Modules\Admin\Models\IdsOfficeSlots', 'ids_office_timing_id', 'id')->orderBy('start_time');
    }

    public function IdsOffice()
    {
        return $this->hasMany('Modules\Admin\Models\IdsOffice', 'ids_office_id', 'id');
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsOfficeSlots extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'display_name', 'ids_office_timing_id', 'ids_office_id', 'start_time', 'end_time',
        'valid_till_date', 'active'
    ];

    public function IdsOfficeSlotBlock()
    {
        return $this->hasMany('Modules\Admin\Models\IdsOfficeSlotBlock', 'ids_office_slot_id', 'id');
    }

    public function IdsEntries()
    {
        return $this->hasMany('Modules\IdsScheduling\Models\IdsEntries', 'ids_office_slot_id', 'id');
    }
}

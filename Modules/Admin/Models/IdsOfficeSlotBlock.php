<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class IdsOfficeSlotBlock extends Model
{
    protected $fillable = ['day_id','slot_block_date','ids_office_id','ids_service_id',
    'ids_office_slot_id','created_by','ids_blocking_request_id','active'];

    public function IdsOfficeSlots(){
        return $this->belongsTo('Modules\Admin\Models\IdsOfficeSlots', 'ids_office_slot_id')->withTrashed();
    }

    public function IdsOffice(){
        return $this->belongsTo('Modules\Admin\Models\IdsOffice', 'ids_office_id')->withTrashed();
    }
}

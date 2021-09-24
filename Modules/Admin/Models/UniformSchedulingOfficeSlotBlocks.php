<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformSchedulingOfficeSlotBlocks extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['uniform_scheduling_office_id','day_id','start_date','start_time','end_date','end_time',
    'created_by','updated_by'];

    public function uniformSchedulingOffices()
    {
        return $this->belongsTo('Modules\Admin\Models\UniformSchedulingOffices',
        'uniform_scheduling_office_id', 'id');
    }

    public function day()
    {
        return $this->belongsTo('Modules\Admin\Models\Days','day_id', 'id');
    }

}

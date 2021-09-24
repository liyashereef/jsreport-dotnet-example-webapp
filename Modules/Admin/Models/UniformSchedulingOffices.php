<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformSchedulingOffices extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['name','adress','latitude','longitude','phone_number_ext'
    ,'phone_number','office_start_time','office_end_time','special_instructions',
    'created_by','updated_by'];

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function UniformSchedulingOfficeTimings(){
        return $this->hasMany('Modules\Admin\Models\UniformSchedulingOfficeTimings', 'uniform_scheduling_office_id', 'id');
    }
    public function UniformSchedulingOfficeSlotBlocks(){
        return $this->hasMany('Modules\Admin\Models\UniformSchedulingOfficeSlotBlocks', 'uniform_scheduling_office_id', 'id');
    }
    public function UniformSchedulingEntries(){
        return $this->hasMany('Modules\UniformScheduling\Models\UniformSchedulingEntries', 'uniform_scheduling_office_id', 'id');
    }
}

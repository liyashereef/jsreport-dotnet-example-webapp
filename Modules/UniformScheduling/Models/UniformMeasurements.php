<?php

namespace Modules\UniformScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformMeasurements extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['user_id','candidate_id','uniform_scheduling_entry_id',
    'uniform_scheduling_measurement_point_id','measurement_values'];

    public function user(){
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

    public function candidate(){
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function uniformSchedulingEntries(){
        return $this->belongsTo('Modules\UniformScheduling\Models\UniformSchedulingEntries', 'uniform_scheduling_entry_id', 'id');
    }

    public function uniformSchedulingMeasurementPoints(){
        return $this->belongsTo('Modules\Admin\Models\UniformSchedulingMeasurementPoints', 'uniform_scheduling_measurement_point_id', 'id');
    }

}

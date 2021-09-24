<?php

namespace Modules\UniformScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Expr\FuncCall;

class UniformSchedulingEntries extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['user_id','uniform_scheduling_office_id','uniform_scheduling_office_timing_id',
    'booked_date','start_time','end_time','email','phone_number','gender','given_interval','given_rate',
    'is_client_show_up','to_be_rescheduled','is_rescheduled','rescheduled_at',
    'rescheduled_id','rescheduled_by','deleted_by','deleted_at','updated_by','notes','is_canceled'];


    public function user(){
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

    public function uniformSchedulingOffice(){
        return $this->belongsTo('Modules\Admin\Models\UniformSchedulingOffices',
        'uniform_scheduling_office_id', 'id');
    }

    public function uniformSchedulingOfficeTiming(){
        return $this->belongsTo('Modules\Admin\Models\UniformSchedulingOfficeTimings',
        'uniform_scheduling_office_timing_id', 'id');
    }

    public function UniformSchedulingCustomQuestionAnswer()
    {
        return $this->hasMany(
            'Modules\Admin\Models\UniformSchedulingCustomQuestionAnswer',
            'uniform_scheduling_entry_id',
            'id'
        )->orderBy('uniform_scheduling_custom_question_id');
    }
    public function updatedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by')->withTrashed();
    }
    public function deletedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'deleted_by')->withTrashed();
    }

    public function uniformMeasurements(){
        return $this->hasMany(
            'Modules\UniformScheduling\Models\UniformMeasurements',
            'uniform_scheduling_entry_id',
            'id'
        );
    }

}

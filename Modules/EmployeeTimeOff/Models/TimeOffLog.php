<?php

namespace Modules\EmployeeTimeOff\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeOffLog extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    public $table = 'time_off_log';
    protected $fillable = ['time_off_id', 'notes','start_date','end_date','days_approved','days_rejected','days_remaining', 'created_by', 'approved'];

    

    public function created_by()
    {
        return $this->belongsTo('Modules\Admin\Models\Employee', 'created_by', 'id')->withTrashed();
    }

 

}

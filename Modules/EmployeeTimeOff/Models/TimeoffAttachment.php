<?php

namespace Modules\EmployeeTimeOff\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeoffAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['timeoff_id', 'attachment_id', 'created_by'];
    

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }

    public function employee_time_off()
    {
        return $this->belongsTo('Modules\EmployeeTimeOff\Models\EmployeeTimeOff', 'timeoff_id', 'id');
    }


}

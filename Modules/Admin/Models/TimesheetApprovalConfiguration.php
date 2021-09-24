<?php

namespace Modules\Admin\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class TimesheetApprovalConfiguration extends Model
{


    protected $fillable = ['day','time','email_1_time','email_2_time','email_3_time','is_previous_week_enabled'];

}





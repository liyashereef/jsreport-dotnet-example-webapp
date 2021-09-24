<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class TimesheetApprovalRatingConfiguration extends Model
{
    protected $fillable = ['timesheet_approval_configurations_id','early','type','untill','difference','rating'];
}

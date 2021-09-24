<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSheetApprovalConfiguration extends Model
{
    public $timestamps = true;
    protected $table = 'timesheet_approval_configurations';
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = ['day','time','date'];
}

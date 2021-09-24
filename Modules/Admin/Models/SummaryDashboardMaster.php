<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SummaryDashboardMaster extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'machine_name', 'threshold_type', 'is_active', 'created_by', 'updated_by'];
    protected $table = 'summary_dashboard_master';
}

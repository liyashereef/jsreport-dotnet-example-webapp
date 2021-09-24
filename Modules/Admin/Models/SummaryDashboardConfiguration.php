<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SummaryDashboardConfiguration extends Model
{
    use SoftDeletes;
    protected $table = 'summary_dashboard_configurations';
    protected $fillable = ['color', 'value', 'type'];
}

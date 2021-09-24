<?php

namespace Modules\FMDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FmDashboardWidget extends Model
{
    use SoftDeletes;
    protected $fillable = ['permission','name','section_name','active'];
}

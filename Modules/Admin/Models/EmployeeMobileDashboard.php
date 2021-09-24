<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeMobileDashboard extends Model
{
    use SoftDeletes;
    protected $fillable = ["user_id", "report_id"];
}

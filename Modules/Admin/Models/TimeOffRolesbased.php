<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeOffRolesbased extends Model
{
    use SoftDeletes;
    protected $table = "time_off_request_type_roles";
    protected $fillable = [
        "timeoff_request_type_setting_id",
        "role_id",
        "role_exception",
        "created_by",
        "updated_by"
    ];
}

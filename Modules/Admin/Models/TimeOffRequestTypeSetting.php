<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeOffRequestTypeSetting extends Model
{
    use SoftDeletes;
    protected $table = "time_off_request_type_settings";
    public $timestamps = true;
    protected $fillable = [
        'min_experience',
        'no_of_leaves',
        'time_off_request_type_id',
        // 'accrual_day',
        // 'accrual_month',
        'reset_term',
        'reset_day',
        'carry_forward',
        'carry_forward_percentage',
        'carry_forward_expires_in_month',
        'encashment_percentage',
        'active',
        'created_by',
        'updated_by'
    ];
    protected $dates = ['deleted_at'];

    public function timeoffRequestType()
    {
        return $this->belongsTo('Modules\Admin\Models\TimeOffRequestTypeLookup', 'time_off_request_type_id', 'id');
    }

    public function timeoffRoles()
    {
        return $this->hasMany('Modules\Admin\Models\TimeOffRolesbased', 'timeoff_request_type_setting_id', 'id');
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserBenefit extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'payroll_group_id', 'vacation_level', 'green_sheild_no', 'is_lacapitale_life_insurance_enrolled', 'created_by', 'updated_by'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id'); //
    }

    /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function payroll_group()
    {
        return $this->belongsTo('Modules\Admin\Models\UserPayrollGroup', 'payroll_group_id', 'id'); //
    }
}

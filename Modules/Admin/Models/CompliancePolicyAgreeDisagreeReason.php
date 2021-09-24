<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CompliancePolicyAgreeDisagreeReason extends Model
{
    use SoftDeletes;
    protected $fillable = ['compliance_policy_id','agree_or_disagree','reason','created_by'];
}

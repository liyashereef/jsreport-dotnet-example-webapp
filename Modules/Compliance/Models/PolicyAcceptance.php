<?php

namespace Modules\Compliance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyAcceptance extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $hidden = ['signature_file_name'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['policy_id', 'employee_id','agree','compliance_policy_agree_disagree_reason_id','comment','signature_file_name'];

    /**
     * Relation to CompliancePolicy table
     * @return type
     */
    public function policy()
    {
        return $this->belongsTo('Modules\Admin\Models\CompliancePolicy', 'policy_id', 'id');
    }

    /**
     * Relation to Employee table
     * @return type
     */
    public function employee()
    {
        return $this->belongsTo('Modules\Admin\Models\Employee', 'employee_id', 'user_id');
    }
    /**
     * Relation to Employee table
     * @return type
     */
    public function employeeWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\Employee', 'employee_id', 'user_id')->withTrashed();
    }

    /**
     * Relation to Agree/Disagree reason table
     * @return type
     */
    public function agreeDisagreeReason()
    {
        return $this->belongsTo('Modules\Admin\Models\CompliancePolicyAgreeDisagreeReason', 'compliance_policy_agree_disagree_reason_id', 'id')->withTrashed();
    }



}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompliancePolicy extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['policy_name', 'compliance_policy_category_id', 'policy_description', 'policy_objectives', 'policy_file', 'status','enable_agree_or_disagree','enable_agree_textbox','enable_disagree_textbox','is_broadcasted'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to CompliancePolicyCategory
     * @return type
     */
    public function category()
    {
        return $this->belongsTo('Modules\Admin\Models\CompliancePolicyCategory', 'compliance_policy_category_id', 'id');
    }

    /**
     * Relation to PolicyAcceptance
     * @return type
     */
    public function policyAccept()
    {
        return $this->HasMany('Modules\Compliance\Models\PolicyAcceptance', 'policy_id', 'id');
    }

    /**
     * Relation to AgreeDisagreeReasons
     * @return type
     */
    public function agreeDisagreeReasons()
    {
        return $this->HasMany('Modules\Admin\Models\CompliancePolicyAgreeDisagreeReason', 'compliance_policy_id', 'id');
    }
    /**
     * Relation to AgreeReasons
     * @return type
     */
    public function agreeReasons()
    {
        return $this->HasMany('Modules\Admin\Models\CompliancePolicyAgreeDisagreeReason', 'compliance_policy_id', 'id')->where('agree_or_disagree', 1);
    }

     /**
     * Relation to DisgreeReasons
     * @return type
     */
    public function disagreeReasons()
    {
        return $this->HasMany('Modules\Admin\Models\CompliancePolicyAgreeDisagreeReason', 'compliance_policy_id', 'id')->where('agree_or_disagree', 0);
    }

    /**
     * Relation to CompliancePolicyRole
     * @return type
     */
    public function roles()
    {
        return $this->HasMany('Modules\Admin\Models\CompliancePolicyRole', 'compliance_policy_id', 'id');
    }
}

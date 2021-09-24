<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompliancePolicyRole extends Model
{
    use SoftDeletes;
    protected $fillable = ['compliance_policy_id','role'];

    /**
     * Relation to CompliancePolicy
     * @return type
     */
    public function policy()
    {
        return $this->HasOne('Modules\Admin\Models\CompliancePolicy','id', 'compliance_policy_id');
    }

}

<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerStcDetail extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['customer_id', 'job_description', 'nmso_account', 'security_clearance_lookup_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Belongs relation to Customer
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Customer', 'customer_id', 'id');
    }

    /**
     * Belongs relation to securityClearanceLookup
     *
     */
    public function security_clearance()
    {
        return $this->belongsTo('Modules\Admin\Models\SecurityClearanceLookup', 'security_clearance_lookup_id', 'id');
    }

    public function trashed_security_clearance()
    {
        return $this->belongsTo('Modules\Admin\Models\SecurityClearanceLookup', 'security_clearance_lookup_id', 'id')->withTrashed();
    }

}

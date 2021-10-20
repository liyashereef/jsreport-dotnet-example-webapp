<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\HelperService;
use \Carbon\Carbon;

class SecurityClearanceUser extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['user_id', 'security_clearance_lookup_id', 'value', 'valid_until'];
    protected $appends = ['status','status_color'];

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
     * Relationship: Security Clearance Lookups
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function securityClearanceLookups()
    {
        return $this->belongsTo('Modules\Admin\Models\SecurityClearanceLookup', 'security_clearance_lookup_id', 'id'); //
    }

    public function getValidUntilTextAttribute() {
        return Carbon::parse($this->valid_until)->toFormattedDateString();
    }

    public function getStatusAttribute() {
        return HelperService::expiryDate($this->valid_until);
    }

    public function getStatusColorAttribute() {
        return HelperService::getExpiryColor($this->valid_until);
    }

}

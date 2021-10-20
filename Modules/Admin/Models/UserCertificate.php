<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\HelperService;
use \Carbon\Carbon;

class UserCertificate extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $appends = ['status','status_color'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'certificate_id', 'expires_on',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];

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
    public function certificateMaster()
    {
        return $this->belongsTo('Modules\Admin\Models\CertificateMaster', 'certificate_id', 'id'); //
    }

    public function trashedCertificateMaster()
    {
        return $this->belongsTo('Modules\Admin\Models\CertificateMaster', 'certificate_id', 'id')->withTrashed(); //
    }

    public function getValidUntilTextAttribute() {
        return Carbon::parse($this->expires_on)->toFormattedDateString();
    }

    public function getStatusAttribute() {
        return HelperService::expiryDate($this->expires_on);
    }

    public function getStatusColorAttribute() {
        return HelperService::getExpiryColor($this->expires_on);
    }

}

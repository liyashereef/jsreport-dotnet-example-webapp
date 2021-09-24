<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class BonusEmployeeData extends Moloquent
{
    protected $connection = "mongodb";
    protected $collection = 'bonus_employee_data';
    protected $created_at = 'Y-m-d H:i:s';
    protected $fillable = [
        "bonus_pool_id",
        "rank_data",
        "rank_day",
        "average_accepted_rate",
        "average_site_rate",
        "per_shift_amount", "created_at", "updated_at"
    ];
    protected $dates = ["rank_day"];
    // use SoftDeletes;
    // protected $fillable = [
    //     "subject",
    //     "message",
    //     "general_clients",
    //     "general_clientgroups",
    //     "general_roles",
    //     "created_by"
    // ];

    /**
     * Get all of the comments for the EmailBlastLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function assignedRoles()
    // {
    //     return $this->hasMany("Modules\Jitsi\Models\EmailBlastRoles", 'blast_mail_id', 'id');
    // }

    // public function assignedClients()
    // {
    //     return $this->hasMany("Modules\Jitsi\Models\EmailBlastClients", 'blast_mail_id', 'id');
    // }

    public function users()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->with('employee')->withTrashed();
    }
}

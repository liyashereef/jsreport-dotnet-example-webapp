<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class EmailBlastLog extends Moloquent
{
    protected $connection = "mongodb";
    protected $collection = 'email_blast_logs';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $created_at = 'Y-m-d H:i:s';
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

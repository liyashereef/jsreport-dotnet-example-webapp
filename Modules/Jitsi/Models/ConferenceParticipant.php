<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConferenceParticipant extends Model
{
    use SoftDeletes;
    protected $fillable = ["sessionid", "userid", "jitsiuserid"];

    public function ConferenceUser()
    {
        return $this->belongsTo("Modules\Admin\Models\User", "userid", "id");
    }
}

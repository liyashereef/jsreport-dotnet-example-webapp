<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConferenceRoom extends Model
{
    use SoftDeletes;
    protected $fillable = ["room_name", "room_password","scheduleroomid", "created_by"];

    public function ConferenceSession()
    {
        return $this->hasOne("Modules\Jitsi\Models\ConferenceSession", "roomid");
    }

    public function ConferenceRecording()
    {
        return $this->hasMany("Modules\Jitsi\Models\ConferenceRecording", "roomid", "id");
    }
}

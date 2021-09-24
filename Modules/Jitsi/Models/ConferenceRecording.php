<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceRecording extends Model
{
    protected $fillable = ["roomid", "sessionid", "recording"];
}

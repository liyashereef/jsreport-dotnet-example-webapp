<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceRecordingServer extends Model
{
    protected $fillable = ['instanceid', 'ip'];
}

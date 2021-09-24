<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledMeetingParticipant extends Model
{
    use SoftDeletes;
    protected $fillable = ["meetingid","userid","status"];
}

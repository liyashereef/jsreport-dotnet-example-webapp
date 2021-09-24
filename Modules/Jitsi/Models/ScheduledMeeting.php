<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledMeeting extends Model
{
    use SoftDeletes;
    protected $fillable = ["title", "startdate", "enddate", "meetinghours", "status", "createdby"];
}

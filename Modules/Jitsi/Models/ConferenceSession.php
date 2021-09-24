<?php

namespace Modules\Jitsi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConferenceSession extends Model
{
    use SoftDeletes;
    protected $fillable = ["roomid", "meetingtitle", "description", "customer_id", "employee_id", "scheduleid", "status"];

    public function ConferenceRoom()
    {
        return $this->belongsTo("Modules\Jitsi\Models\ConferenceRoom", "roomid", "id");
    }
}

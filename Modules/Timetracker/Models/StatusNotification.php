<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class StatusNotification extends Model
{

    public $timestamps = true;
    protected $fillable = ['user_id', 'notification_id', 'active'];

    public function user()
    {
        return $this->belongsTo('Module\Admin\Models\User', 'user_id', 'id');
    }

    public function notification()
    {
        return $this->belongsTo('Modules\Timetracker\Models\Notification', 'notification_id', 'id');
    }

}

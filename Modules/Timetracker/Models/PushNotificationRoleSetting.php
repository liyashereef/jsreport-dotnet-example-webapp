<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\User;

class PushNotificationRoleSetting extends Model
{
    use SoftDeletes;
    protected $fillable = ['role', 'push_notification_type_id', 'created_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function push_notification_type()
    {
        return $this->belongsTo(PushNotificationType::class, 'push_notification_type_id', 'id');
    }

}

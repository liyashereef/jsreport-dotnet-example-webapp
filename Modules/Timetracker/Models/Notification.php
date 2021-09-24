<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['notification_message', 'employee_id', 'supervisor_id', 'read'];

    /**
     * Relation to user notification guard
     */
    public function user_notification_guard()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id');
    }

    /**
     * Relation to status notification
     */
    public function status_notification()
    {
        return $this->hasMany('Modules\Timetracker\Models\StatusNotification', 'notification_id', 'id');
    }

}

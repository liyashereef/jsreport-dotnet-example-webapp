<?php

namespace Modules\ProjectManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PmTaskOwner extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $table = 'pm_task_owners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['task_id', 'user_id', 'type'];

    /**
     * The customer details that belongs to project management
     *
     */
    public function taskDetails()
    {
        return $this->belongsTo('Modules\ProjectManagement\Models\PmTask', 'task_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function userWithOutTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

}

<?php

namespace Modules\ProjectManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PmTaskStatus extends Model
{

    use SoftDeletes;
    protected $touches = ['taskDetails'];
    public $timestamps = true;
    protected $table = 'pm_task_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['percentage', 'task_id', 'notes', 'status_date', 'updated_by'];

    /**
     * The customer details that belongs to project management
     *
     */
    public function taskDetails()
    {
        return $this->belongsTo('Modules\ProjectManagement\Models\PmTask', 'task_id', 'id');
    }

    /**
     * The updated user details that belongs to project management with trashed
     *
     */
    public function updatedBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by', 'id')->withTrashed();
    }
}

<?php

namespace Modules\ProjectManagement\Models;

use Illuminate\Database\Eloquent\Model;

class PmTaskStatusLog extends Model
{
    protected $fillable = [
        'task_id',
        'modified_by',
        'old_value',
        'new_value'
    ];
}

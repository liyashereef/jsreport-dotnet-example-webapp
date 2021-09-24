<?php

namespace Modules\CapacityTool\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapacityTool extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'employee_id', 'question_id', 'answer', 'answer_type', 'rating_status_id', 'comment', 'capacity_tool_entry_id',
        'created_by',
    ];

    public function question()
    {
        return $this->belongsTo('Modules\CapacityTool\Models\CapacityToolQuestion', 'question_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id');
    }

    public function answerable()
    {
        //morphTo(string $name = null, string $type = null, string $id = null, string $ownerKey = null)
        return $this->morphTo('answerable', 'answer_type', 'answer')->withTrashed();

    }

    public function capacitytoolentry()
    {
        return $this->hasOne('Modules\CapacityTool\Models\CapacityToolEntry', 'id', 'capacity_tool_entry_id');
    }

}

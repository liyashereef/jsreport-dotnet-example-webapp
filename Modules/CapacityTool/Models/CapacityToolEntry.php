<?php

namespace Modules\CapacityTool\Models;

use Illuminate\Database\Eloquent\Model;

class CapacityToolEntry extends Model
{
    protected $fillable = ['employee_id'];

    /**
     * Capacity tool question and answers related to this entry
     */

    public function capacitytools()
    {
        return $this->hasMany('Modules\CapacityTool\Models\CapacityTool');
    }

    /***
     * Capacity tool parentquestion and answers related to this entry
     */

    public function parentcapacitytools()
    {
        return $this->hasMany('Modules\CapacityTool\Models\CapacityTool')->whereIn('question_id', [1, 6, 4, 8, 9, 16, 17, 18]);
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id');
    }

}

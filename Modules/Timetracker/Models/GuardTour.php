<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class GuardTour extends Model
{

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'shift_id','submitted_date', 'submitted_time', 'notes', 'image',
    ];

    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift', 'shift_id', 'id');
    }

}

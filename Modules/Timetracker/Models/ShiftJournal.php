<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftJournal extends Model
{

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'shift_id','submitted_date', 'submitted_time','shift_start_time', 'notes', 'image', 'customer_id', 'created_by',
    ];

    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift', 'shift_id', 'id');
    }

    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

}

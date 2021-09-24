<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeUnavailability extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'employee_id',
        'from',
        'to',
        'shift_id',
        'comments',
        'created_by',
    ];

    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id');
    }

}

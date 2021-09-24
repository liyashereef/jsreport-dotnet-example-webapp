<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkHourActivityCodeCustomers extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'work_hour_type_id',
        'customer_type_id',
        'code',
        'description',
        'created_by',
        'updated_by'
    ];
}

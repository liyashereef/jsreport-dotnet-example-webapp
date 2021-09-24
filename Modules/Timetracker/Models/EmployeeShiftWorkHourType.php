<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeShiftWorkHourType extends Model
{

    use SoftDeletes;
    protected $fillable = ['name','description','sort_order','is_editable','is_deletable','created_by','updated_by'];
}

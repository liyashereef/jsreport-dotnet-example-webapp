<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPayrollGroup extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'apogee_code', 'created_by', 'updated_by'];
}

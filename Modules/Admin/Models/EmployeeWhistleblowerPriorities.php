<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class EmployeeWhistleblowerPriorities extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['priority','rank'];
    protected $dates = ['deleted_at'];
}

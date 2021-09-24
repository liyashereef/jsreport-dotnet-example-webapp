<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeWhistleblowerCategories extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['roles','shortname'];
    protected $dates = ['deleted_at'];
}

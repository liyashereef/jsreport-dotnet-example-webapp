<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchRequestType extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['id','name','rate','description'];
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExitTerminationReasonLookup extends Model
{
    use SoftDeletes;
    
    public $timestamps = true;
    protected $fillable = ['reason','shortname'];
    protected $dates = ['deleted_at'];
}
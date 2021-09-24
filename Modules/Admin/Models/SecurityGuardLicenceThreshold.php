<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecurityGuardLicenceThreshold extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['threshold'];
    protected $dates = ['deleted_at'];
}

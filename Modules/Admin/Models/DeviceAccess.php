<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class DeviceAccess extends Model
{
    use SoftDeletes;

    public $table = 'device_accesses';
    protected $fillable = ['DeviceType','status','createdby'];
    protected $dates = ['deleted_at'];
}

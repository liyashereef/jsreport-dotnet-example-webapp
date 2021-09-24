<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PermissionMapping extends Model
{
    use SoftDeletes;

    public $table = 'permission_mappings';
    protected $fillable = ['role_id','permission_id'];
    protected $dates = ['deleted_at'];
}

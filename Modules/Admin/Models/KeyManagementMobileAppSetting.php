<?php

namespace Modules\Admin\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class KeyManagementMobileAppSetting extends Model
{
    public $timestamps = true;
    protected $fillable = ['keymanagement_module_image_limit'];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
}

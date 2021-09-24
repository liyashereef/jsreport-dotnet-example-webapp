<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingPageModuleWidget extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['name'];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

}

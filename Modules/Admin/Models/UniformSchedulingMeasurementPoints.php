<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformSchedulingMeasurementPoints extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['name','is_active'];

}

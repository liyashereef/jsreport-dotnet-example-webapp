<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiThresholdColor extends Model
{
    use SoftDeletes;
    protected $fillable = ['color','color_code','font_color','created_by','updated_by'];
}

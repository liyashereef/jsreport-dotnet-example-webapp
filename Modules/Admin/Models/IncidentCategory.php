<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentCategory extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','category_short_name'];
}

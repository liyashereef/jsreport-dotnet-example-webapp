<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    //
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['color_name', 'color_class_name',];

}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModuleFieldType extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['id','type_name'];

    

}

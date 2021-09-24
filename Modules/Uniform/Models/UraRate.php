<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UraRate extends Model
{
    use SoftDeletes;
    protected $fillable = ['amount','created_by'];
}

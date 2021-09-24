<?php

namespace Modules\Admin\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class StatHolidays extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['holiday', 'description'];
}

<?php

namespace Modules\Admin\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class AdminColorsetting extends Model
{
    use SoftDeletes;
    protected $fillable = ['title','colorhexacode','fieldidentifier','rangebegin','rangeend','status','created_by'];
}

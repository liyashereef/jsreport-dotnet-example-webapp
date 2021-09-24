<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhistleblowerStatusLookup extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','status','initial_status'];
}

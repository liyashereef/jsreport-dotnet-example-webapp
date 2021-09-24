<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorStatusLookups extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','is_default','is_authorised'];
}

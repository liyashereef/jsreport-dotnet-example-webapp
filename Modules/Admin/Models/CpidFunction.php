<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CpidFunction extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'created_by', 'updated_by'];
}

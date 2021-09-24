<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaritalStatus extends Model
{
    use SoftDeletes;
    protected $fillable = ['status', 'apogee_code', 'created_by', 'updated_by'];
}

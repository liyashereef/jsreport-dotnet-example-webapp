<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banks extends Model
{
    use SoftDeletes;
    protected $fillable = ['bank_name', 'bank_code', 'created_by', 'updated_by'];
}

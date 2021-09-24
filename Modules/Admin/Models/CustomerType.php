<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'created_by', 'updated_by'];
}

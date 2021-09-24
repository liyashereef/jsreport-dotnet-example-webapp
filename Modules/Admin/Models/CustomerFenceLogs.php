<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFenceLogs extends Model
{
    protected $fillable = ['fenceid','unit'];
}

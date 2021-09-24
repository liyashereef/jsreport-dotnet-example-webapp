<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobProcessLookup extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'process_name',

    ];
}

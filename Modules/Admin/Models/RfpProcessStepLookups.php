<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpProcessStepLookups extends Model
{
    use SoftDeletes;
    protected $table = "rfp_process_steps";

    protected $fillable = ['id','process_steps','step_number'];
}


<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpResponseTypeLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['id','rfp_response_type'];
}

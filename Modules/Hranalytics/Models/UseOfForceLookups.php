<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UseOfForceLookups extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ["use_of_force", "order_sequence"];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

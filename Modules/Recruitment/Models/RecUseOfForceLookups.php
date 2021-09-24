<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecUseOfForceLookups extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $fillable = ["use_of_force", "order_sequence"];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

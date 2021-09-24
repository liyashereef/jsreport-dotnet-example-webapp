<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeRatingLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['rating', 'score', 'shortname'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}

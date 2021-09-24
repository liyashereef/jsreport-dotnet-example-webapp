<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateExperienceLookups extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'experience_ratings', 'score',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}

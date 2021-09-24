<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class YearToDate extends Model
{

    //use SoftDeletes;

    public $timestamps = true;
    protected $table = 'year_to_date';
    protected $fillable = ['year_to_date'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

}

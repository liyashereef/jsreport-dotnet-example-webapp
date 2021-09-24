<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecProcessSteps extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    public $timestamps = true;
    protected $fillable = ['step_order','step_name','display_name','notes','type','route','tab_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

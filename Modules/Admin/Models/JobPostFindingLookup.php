<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPostFindingLookup extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'job_post_finding',
        'order_sequence',
        'is_editable',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}

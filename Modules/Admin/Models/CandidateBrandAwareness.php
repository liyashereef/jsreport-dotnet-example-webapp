<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CandidateBrandAwareness extends Model
{
    //
    use SoftDeletes;
    public $timestamps = true;
    protected $table = 'candidate_brand_awareness';

    protected $fillable = ['answer','order_sequence'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

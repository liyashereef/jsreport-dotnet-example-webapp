<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecTrainingTimingLookup extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_training_timing_lookups';
    public $timestamps = true;
    protected $fillable = ['training'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

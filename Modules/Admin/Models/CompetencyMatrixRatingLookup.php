<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetencyMatrixRatingLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'rating', 'order_sequence',
    ];
}

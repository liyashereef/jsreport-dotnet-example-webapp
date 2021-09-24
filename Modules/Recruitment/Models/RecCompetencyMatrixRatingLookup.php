<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCompetencyMatrixRatingLookup extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_competency_matrix_rating_lookups';
    public $timestamps = true;

    protected $fillable = [
        'rating', 'order_sequence',
    ];
}

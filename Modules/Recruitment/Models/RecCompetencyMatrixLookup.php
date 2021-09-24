<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCompetencyMatrixLookup extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_competency_matrix_lookups';
    public $timestamps = true;

    protected $fillable = [
        'competency_matrix_category_id',
        'competency',
        'definition',
        'behavior',
    ];

    public function category(){
        return $this->belongsTo('Modules\Recruitment\Models\RecCompetencyMatrixCategoryLookup', 'competency_matrix_category_id', 'id')->withTrashed();
    }
}

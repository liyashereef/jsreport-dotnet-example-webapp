<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCompetencyMatrixCategoryLookup extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_competency_matrix_category_lookups';
    public $timestamps = true;

    protected $fillable = [
        'category_name',
        'short_name'
    ];

    public function competency(){
        return $this->HasMany('Modules\Recruitment\Models\RecCompetencyMatrixLookup');
    }
}

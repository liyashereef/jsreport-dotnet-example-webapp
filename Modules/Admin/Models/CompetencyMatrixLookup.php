<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetencyMatrixLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'competency_matrix_category_id',
        'competency',
        'definition',
        'behavior',
    ];

    public function category(){
        return $this->belongsTo('Modules\Admin\Models\CompetencyMatrixCategoryLookup', 'competency_matrix_category_id', 'id')->withTrashed();
    }
}

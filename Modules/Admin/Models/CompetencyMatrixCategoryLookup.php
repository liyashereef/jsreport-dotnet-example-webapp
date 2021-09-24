<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetencyMatrixCategoryLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'category_name',
        'short_name'
    ];

    public function competency(){
        return $this->HasMany('Modules\Admin\Models\CompetencyMatrixLookup');
    }
}

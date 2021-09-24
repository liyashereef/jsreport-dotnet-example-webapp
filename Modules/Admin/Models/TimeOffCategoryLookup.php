<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeOffCategoryLookup extends Model
{
    //
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['type','description','reference','allowed_days','allowed_hours','allowed_weeks'];
    protected $table = 'timeoff_category_lookup';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

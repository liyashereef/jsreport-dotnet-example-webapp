<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StcReportingTemplateRule extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['color_id', 'min_value', 'max_value'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to color
     *
     * @return type
     */
    public function color()
    {
        return $this->belongsTo('Modules\Admin\Models\Color');
    }

}

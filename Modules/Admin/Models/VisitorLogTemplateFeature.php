<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogTemplateFeature extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['template_id','feature_name','feature_displayname','is_required','is_visible'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

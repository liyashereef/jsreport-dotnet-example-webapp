<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogTemplateFields extends Model {

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['template_id','fieldname','field_displayname','field_type','is_required','is_visible','is_custom'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}

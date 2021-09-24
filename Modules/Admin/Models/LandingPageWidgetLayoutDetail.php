<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingPageWidgetLayoutDetail extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['landing_page_widget_layout_id', 'row_index', 'column_index', 'rowspan', 'colspan'];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
}

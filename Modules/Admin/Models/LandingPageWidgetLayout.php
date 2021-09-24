<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingPageWidgetLayout extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['name', 'no_of_rows', 'no_of_columns'];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function landingPageWidgetLayoutDetail() {
        return $this->hasMany('Modules\Admin\Models\LandingPageWidgetLayoutDetail', 'landing_page_widget_layout_id', 'id');
    }
}

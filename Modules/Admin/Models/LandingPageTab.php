<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingPageTab extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'customer_id', 
        'tab_name', 
        'landing_page_widget_layout_id',
        'seq_no',
        'default_tab_structure',
        'active',
        'created_by'
    ];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
    
    
    public function tabDetails() {
        return $this->hasMany('Modules\Admin\Models\LandingPageTabDetail', 'landing_page_tab_id', 'id');
    }

    public function widgetLayouts() {
        return $this->belongsTo('Modules\Admin\Models\LandingPageWidgetLayout', 'landing_page_widget_layout_id', 'id');
    }
}
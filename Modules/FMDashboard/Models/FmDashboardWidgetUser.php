<?php

namespace Modules\FMDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\User;

class FmDashboardWidgetUser extends Model
{
    protected $fillable = ['user_id','fm_dashboard_widget_id'];

    public function widget()
    {
        return $this->belongsTo(FmDashboardWidget::class,'fm_dashboard_widget_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}

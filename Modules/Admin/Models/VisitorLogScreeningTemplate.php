<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogScreeningTemplate extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $table = 'visitor_log_screening_templates';
    protected $fillable = ['name','description','created_by','updated_by'];

    public function VisitorLogScreeningTemplateCustomerAllocation()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogScreeningTemplateCustomerAllocation', 'visitor_log_screening_template_id', 'id');
    }

    public function VisitorLogScreeningTemplateQuestion(){
        return $this->hasMany('Modules\Admin\Models\VisitorLogScreeningTemplateQuestion', 'visitor_log_screening_template_id', 'id');
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogTemplates extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['template_name'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get supervisor for a customer
     * @return type
     */
    public function visitorLogTemplate()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogCustomerTemplateAllocation', 'template_id', 'id');
    }

    public function VisitorLogScreeningTemplateQuestion()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogScreeningTemplateQuestion', 'visitor_log_screening_template_id', 'id');
    }

    /**
     * The user that belongs to employee allocation
     *
     */
    public function template_feature()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogTemplateFeature', 'template_id', 'id');
    }
    /**
     * The user that belongs to employee allocation
     *
     */
    public function template_fields()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogTemplateFields', 'template_id', 'id');
    }

    /**
     * List only visible template fields.
     *
     */
    public function visible_template_fields()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogTemplateFields', 'template_id', 'id')->where('is_visible',1);
    }
}

<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReport extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['customer_payperiod_template_id', 'element_id', 'question', 'answer', 'score', 'created_by', 'updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function customerPayperiodTemplate()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\CustomerPayperiodTemplate');
    }

    public function templateForm()
    {
        return $this->belongsTo('Modules\Admin\Models\TemplateForm', 'element_id', 'id');
    }
    
    public function templateFormWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\TemplateForm', 'element_id', 'id')->withTrashed();
    }    

    public function parentTemplateForm()
    {
        return $this->belongsTo('Modules\Admin\Models\TemplateForm', 'element_id', 'id')->whereNull('parent_position');
    }
    
    public function parentTemplateFormWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\TemplateForm', 'element_id', 'id')->withTrashed()->whereNull('parent_position');
    }    

}

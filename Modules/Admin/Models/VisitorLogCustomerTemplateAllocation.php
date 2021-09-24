<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogCustomerTemplateAllocation extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['template_id', 'customer_id', 'created_by', 'updated_by'];

    /**
     * The user that belongs to employee allocation
     *
     */
    public function template()
    {
        return $this->belongsTo('Modules\Admin\Models\VisitorLogTemplates', 'template_id', 'id');
    }
    /**
     * The user that belongs to employee allocation
     *
     */
    public function template_feature()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogTemplateFeature', 'template_id', 'template_id');
    }
    /**
     * The user that belongs to employee allocation
     *
     */
    public function template_fields()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogTemplateFields', 'template_id', 'template_id');
    }
    /**
     * The customer that belongs to employee allocation
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }
      /**
     * List only visible template fields.
     *
     */
    public function visible_template_fields()
    {
        return $this->hasMany('Modules\Admin\Models\VisitorLogTemplateFields', 'template_id', 'template_id')->where('is_visible',1);
    }

}

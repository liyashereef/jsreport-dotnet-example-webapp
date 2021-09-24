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

}

<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayperiodTemplate extends Model
{
    public $timestamps = true;
    protected $fillable = ['customer_id', 'payperiod_id', 'template_id', 'created_by', 'updated_by'];

    public function template()
    {
        return $this->belongsTo('Modules\Admin\Models\Template', 'template_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    public function customerTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function payperiod()
    {
        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'payperiod_id', 'id');
    }

    public function payperiodTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'payperiod_id', 'id')->withTrashed();
    }

    public function customerReport()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\CustomerReport')->orderBy('element_id','asc');
    }
}

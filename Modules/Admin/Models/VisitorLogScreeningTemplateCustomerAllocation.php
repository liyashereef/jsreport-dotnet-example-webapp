<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogScreeningTemplateCustomerAllocation extends Model
{
    use SoftDeletes;
    protected $table = 'visitor_log_screening_template_customer_allocations';
    protected $fillable = ['visitor_log_screening_template_id','customer_id','created_by','updated_by'];

    public function Customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    public function VisitorLogScreeningTemplate(){
        return $this->belongsTo('Modules\Admin\Models\VisitorLogScreeningTemplate', 'visitor_log_screening_template_id', 'id');
    }

}

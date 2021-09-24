<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkHourActivityCodeCustomer extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['work_hour_type_id','customer_type_id','code','duplicate_code','description','created_by','updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function work_hour_type()
    {

        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShiftWorkHourType', 'work_hour_type_id', 'id');
    }

    public function customer_type()
    {

        return $this->belongsTo('Modules\Admin\Models\CustomerType', 'customer_type_id', 'id');
    }

    public function work_hour_type_trashed()
    {

        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShiftWorkHourType', 'work_hour_type_id', 'id')->withTrashed();
    }

    public function customer_type_trashed()
    {

        return $this->belongsTo('Modules\Admin\Models\CustomerType', 'customer_type_id', 'id')->withTrashed();
    }
}

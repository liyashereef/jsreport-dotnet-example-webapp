<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class MobileSecurityPatrol extends Model
{

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shift_id', 'customer_id', 'subject_id', 'user_id', 'description'];

    
    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift','shift_id','id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User');
    }

       /**
     * Relation towards customer master
     *
     * @return void
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function subject()
    {
        return $this->belongsTo('Modules\Admin\Models\MobileSecurityPatrolSubject', 'subject_id', 'id')->withTrashed();
    }

}

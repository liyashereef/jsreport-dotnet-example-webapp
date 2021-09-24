<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformSchedulingOfficeTimings extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['uniform_scheduling_office_id', 'start_time', 'end_time', 'start_date',
    'expiry_date', 'intervals', 'created_by', 'updated_by'];

    public function UniformSchedulingOffices()
    {
        return $this->hasMany('Modules\Admin\Models\UniformSchedulingOffices',
        'uniform_scheduling_office_id', 'id');
    }

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentReportSubject extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['subject','subject_short_name','incident_category_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function incident_category()
    {
        return $this->belongsTo(IncidentCategory::class)->withTrashed();
    }

    /**
     * The customer that belongs to employee allocation
     *
     */
    public function customer_allocation()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerIncidentSubjectAllocation', 'subject_id', 'id');
    }

}

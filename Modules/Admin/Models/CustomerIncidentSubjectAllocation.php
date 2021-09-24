<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class CustomerIncidentSubjectAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_id', 'subject_id', 'category_id', 'priority_id', 'incident_response_time', 'sop'];

    /**
     * The customer that belongs to employee allocation
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    /**
     * The customer that belongs to employee allocation
     *
     */
    public function subject()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentReportSubject', 'subject_id', 'id');
    }

    public function subjectWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentReportSubject', 'subject_id', 'id')->withTrashed();
    }

    /* public function customerPriority()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerIncidentPriority', 'customer_incident_priority_id', 'id');
    }*/

    public function incidentPriority()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentPriorityLookup', 'priority_id', 'id');
    }

    public function incidentPriorityWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentPriorityLookup', 'priority_id', 'id')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentCategory', 'category_id', 'id');
    }

    public function categoryWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentCategory', 'category_id', 'id')->withTrashed();;
    }

    public function incidentReport()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\IncidentReport', 'subject_id', 'subject_id');;
    }
}

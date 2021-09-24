<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\IncidentReportSubject;

class IncidentReport extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['description', 'subject_id', 'attachment', 'attachment_id', 'customer_id', 'payperiod_id', 'created_by', 'updated_by', 'source', 'time_of_day', 'occurance_datetime', 'incident_report_uploaded', 'title', 'priority_id'];
  

    public function incidentStatusLog()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\IncidentStatusLog');
    }

    public function incidentStatusLogWtihList()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\IncidentStatusLog')->where('amendment', 0)->with('incidentStatusList', 'user');
    }

    public function latestIncidentStatusLogWtihList()
    {
        return $this->hasOne('Modules\Supervisorpanel\Models\IncidentStatusLog')->where('amendment', 0)->with('incidentStatusList', 'user')->latest();
    }
    public function incidentSuggestedStatusLogWtihList()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\IncidentStatusLog')->where('amendment', 1)->with('incidentSuggestedStatusList');
    }
    public function amendmentList()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\IncidentStatusLog')->with('incidentStatusList', 'user')->orderby('created_at', 'desc');
    }

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }
    public function priority()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentPriorityLookup', 'priority_id', 'id');
    }

    public function incidentAttachment()
    {
        return $this->hasMany('Modules\Supervisorpanel\Models\IncidentAttachment', 'incident_id');
    }

    /**
     * Relation with Customer
     *
     * @return void
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    /**
     * Relation with Payperiod
     *
     * @return void
     */
    public function payperiod()
    {
        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'payperiod_id', 'id')->whereActive(true);
    }

    /**
     * Relation with payperiod including deleted
     *
     * @return type
     */
    public function payperiodWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'payperiod_id', 'id');
    }

    /**
     * Last status
     *
     * @return void
     */
    public function latestStatus()
    {
        return $this->hasOne('Modules\Supervisorpanel\Models\IncidentStatusLog')->latest();
    }

    /**
     * Creator of the incident report
     *
     * @return void
     */
    public function reporter()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function incident_report_subject()
    {
        return $this->belongsTo(IncidentReportSubject::class, 'subject_id', 'id')->withTrashed();
    }

    public function incident_status_logs()
    {
        return $this->hasOne(IncidentStatusLog::class, 'incident_report_id', 'id')->withTrashed();
    }

    public function getIncidentDescriptionAttribute()
    {
        return $this->incidentStatusLog->first()->notes;
    }

    public function getSubjectNameWithFallbackAttribute()
    {
        $subject = $this->incident_report_subject;
        if (is_object($subject)) {
            return $subject->subject;
        }
        return empty($this->description) ? '--' : $this->description;
    }
}

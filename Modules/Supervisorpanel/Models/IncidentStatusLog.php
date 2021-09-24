<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentStatusLog extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['incident_report_id', 'incident_status_list_id', 'notes', 'created_by', 'updated_by','closed_time','suggested_incident_status_list_id','amendment'];
    
    
    public function incidentReport()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\IncidentReport');
    }
    
    public function incidentStatusList()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\IncidentStatusList', 'incident_status_list_id', 'id');
    }
    public function incidentSuggestedStatusList()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\IncidentStatusList', 'suggested_incident_status_list_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }
}

<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['incident_id', 'attachment_id', 'short_description'];

    public function incident_report()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\IncidentReport', 'incident_id', 'id');
    }

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }

}

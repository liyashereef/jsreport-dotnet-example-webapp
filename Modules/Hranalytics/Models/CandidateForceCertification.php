<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateForceCertification extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'force', 'use_of_force_lookups_id', 'expiry'];

    public function force_lookup()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\UseOfForceLookups', 'use_of_force_lookups_id', 'id');
    }

    public function forceAttachmentDetails()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }
}

<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateForceCertification extends Model
{
    public $timestamps = true;
   
    protected $connection = 'mysql_rec';
    protected $fillable = [
        'candidate_id',
        'force',
        'use_of_force_lookups_id',
        'expiry'
    ];

    public function force_lookup()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUseOfForceLookups', 'use_of_force_lookups_id', 'id');
    }

    public function forceAttachmentDetails()
    {
        return $this->setConnection('mysql')->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }
}

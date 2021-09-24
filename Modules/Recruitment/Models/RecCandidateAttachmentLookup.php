<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateAttachmentLookup extends Model
{
    //
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $fillable = ['attachment_name', 'job_id'];

    public function candidateAttachment()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateAttachment', 'attachment_id', 'id');
    }
    public function job()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecJob', 'job_id', 'id');
    }
}

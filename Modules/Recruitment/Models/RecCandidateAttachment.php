<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateAttachment extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_attachments';
    protected $fillable = ['candidate_id', 'attachment_id', 'attachment_file_name'];
    //
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    public function attachment()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateAttachmentLookup', 'attachment_id', 'id');
    }
}

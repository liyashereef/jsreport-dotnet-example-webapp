<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateAttachment extends Model
{

    public $timestamps = true;

    protected $fillable = ['candidate_id', 'attachment_id', 'attachment_file_name'];
    //
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function attachment()
    {
        return $this->belongsTo('Modules\Admin\Models\CandidateAttachmentLookup', 'attachment_id', 'id');
    }
}

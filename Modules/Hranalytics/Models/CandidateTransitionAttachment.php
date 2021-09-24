<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateTransitionAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['candidate_transition_id', 'attachment_id'];

    public function transition()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\CandidateEmployee', 'candidate_transition_id', 'id');
    }

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }

}

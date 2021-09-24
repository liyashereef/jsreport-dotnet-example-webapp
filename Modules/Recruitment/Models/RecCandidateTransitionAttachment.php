<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateTransitionAttachment extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_transition_attachments';
    public $timestamps = true;

    protected $fillable = ['transition_id', 'attachment_id'];

    public function transition()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateEmployee', 'transition_id', 'id');
    }

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }
}

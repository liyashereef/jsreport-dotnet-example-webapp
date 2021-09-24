<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateTermination extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_terminations';

    protected $fillable = ['candidate_id', 'reason_id','reason','user_id'];


    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    public function user(){
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function reasonLookup(){
        return $this->belongsTo('Modules\Admin\Models\CandidateTerminationReasonLookup', 'reason_id', 'id')->withTrashed();
    }
}

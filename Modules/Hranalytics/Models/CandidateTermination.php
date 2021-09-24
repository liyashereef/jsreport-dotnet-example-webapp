<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateTermination extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'reason_id','reason','user_id'];

    
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function user(){
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function reasonLookup(){
        return $this->belongsTo('Modules\Admin\Models\CandidateTerminationReasonLookup', 'reason_id', 'id')->withTrashed();
    }
}

<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateEmployee extends Model
{

    public $timestamps = true;

    protected $fillable = ['candidate_id', 'user_id', 'updated_by'];
    //
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }
    public function clearanceuser()
    {
        return $this->hasMany('Modules\Admin\Models\SecurityClearanceUser', 'user_id', 'user_id');
    }

    public function updatedUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by', 'id');
    }

    public function attachment()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\CandidateTransitionAttachment', 'id', 'candidate_transition_id');
    }

}

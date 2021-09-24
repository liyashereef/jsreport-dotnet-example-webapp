<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateEmployee extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_employees';
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'user_id', 'updated_by'];
    //
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruiting\Models\RecCandidate', 'candidate_id', 'id');
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

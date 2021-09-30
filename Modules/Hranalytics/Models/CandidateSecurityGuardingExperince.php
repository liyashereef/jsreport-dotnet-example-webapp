<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateSecurityGuardingExperince extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'guard_licence', 'start_date_guard_license', 'start_date_first_aid', 'start_date_cpr', 'expiry_guard_license', 'expiry_first_aid', 'expiry_cpr', 'security_clearance', 'security_clearance_type', 'security_clearance_expiry_date', 'years_security_experience', 'most_senior_position_held', 'positions_experinces','test_score_percentage','test_score_document_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo('Modules\Admin\Models\PositionLookup', 'most_senior_position_held', 'id');
    }

    public function testScoreAttachmentDetails()
    {
        return $this->belongsTo('App\Models\Attachment', 'test_score_document_id', 'id');
    }
}
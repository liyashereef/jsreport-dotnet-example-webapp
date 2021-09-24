<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateScreeningQuestionLookups extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_screening_question_lookups';
    public $timestamps = true;

}

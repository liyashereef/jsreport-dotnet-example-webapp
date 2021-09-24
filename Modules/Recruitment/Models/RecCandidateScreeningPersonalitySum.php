<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateScreeningPersonalitySum extends Model
{
    protected $connection = 'mysql_rec';
    protected $fillable = ['candidate_id','column','option','sum'];
}

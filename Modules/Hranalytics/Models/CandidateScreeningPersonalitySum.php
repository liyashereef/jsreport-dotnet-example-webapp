<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateScreeningPersonalitySum extends Model
{
    protected $fillable = ['candidate_id','column','option','sum'];
}

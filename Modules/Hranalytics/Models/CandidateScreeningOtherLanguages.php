<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateScreeningOtherLanguages extends Model
{
    use SoftDeletes;

    protected $fillable = ['candidate_id', 'language_id', 'speaking', 'reading', 'writing'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    /**
     *
     */
    public function language_lookup()
    {
        return $this->belongsTo('Modules\Admin\Models\Languages', 'language_id', 'id');
    }
}

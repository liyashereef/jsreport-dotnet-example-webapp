<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateLanguage extends Model
{

    public $timestamps = true;
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
    public function language_looukp()
    {
        return $this->belongsTo('Modules\Admin\Models\LanguageLookup', 'language_id', 'id');
    }

}

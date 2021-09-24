<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateLanguage extends Model
{
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_languages';
    public $timestamps = true;
    protected $fillable = ['candidate_id', 'language_id', 'speaking', 'reading', 'writing'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    /**
     *
     */
    public function language_looukp()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\LanguageLookup', 'language_id', 'id');
    }

}

<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateScreeningOtherLanguages extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';

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
    public function language_lookup()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\Languages', 'language_id', 'id');
    }
}

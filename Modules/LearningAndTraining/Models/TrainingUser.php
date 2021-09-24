<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TrainingUser extends Model
{
  //  use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['model_name', 'model_id'];

    public function recCandidateTrashed()
    {
       return $this->setConnection('mysql_rec')->belongsTo('Modules\Recruitment\Models\RecCandidate', 'model_id', 'id')->withTrashed();
    }

}

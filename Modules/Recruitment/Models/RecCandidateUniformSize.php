<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateUniformSize extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_uniform_sizes';
    public $timestamps = true;
    protected $fillable = ['candidate_id','kit_id','customer_id','measurement_id','measurement_value'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    public function uniform_measurement_points()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecUniformMeasurementPoint', 'measurement_id', 'id');
    }
}

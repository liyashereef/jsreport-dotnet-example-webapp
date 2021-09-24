<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpEvaluationCriteria extends Model
{
    use SoftDeletes;
    protected $fillable = ['rfp_details_id', 'criteria_name', 'points', 'notes'];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function rfpDetails()
    {
        return $this->belongsTo('Modules\Contracts\Models\RfpDetails', 'rfp_details_id', 'id');
    }

}

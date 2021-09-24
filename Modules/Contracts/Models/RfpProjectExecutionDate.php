<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpProjectExecutionDate extends Model
{
    use SoftDeletes;
    protected $fillable = ['rfp_details_id', 'project_execution_other_date_label', 'project_execution_other_date_value'];
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

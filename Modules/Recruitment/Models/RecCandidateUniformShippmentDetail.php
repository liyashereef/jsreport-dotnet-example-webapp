<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateUniformShippmentDetail extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_uniform_shippment_details';
    public $timestamps = true;
    protected $fillable = ['candidate_id', 'kit_id', 'shippment_status','shippment_address', 'status_date_time'];

    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    public function customerUniformKit()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCustomerUniformKit', 'kit_id', 'id');
    }
    public function shippmentDetailsLog()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateUniformShippmentDetailStatusLog', 'rec_candidate_uniform_shippment_details_id', 'id');
    }
}

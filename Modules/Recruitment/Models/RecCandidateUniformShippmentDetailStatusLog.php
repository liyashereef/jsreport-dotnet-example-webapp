<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateUniformShippmentDetailStatusLog extends Model
{

    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_uniform_shippment_details_status_logs';
    public $timestamps = true;
    protected $fillable = ['rec_candidate_uniform_shippment_details_id', 'status', 'datetime','created_by'];

    public function shippmentDetails()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateUniformShippmentDetail', 'rec_candidate_uniform_shippment_details_id', 'id');
    }
    public function createdUser()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\User', 'created_by', 'id');
    }
}

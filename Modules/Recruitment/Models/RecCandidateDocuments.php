<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateDocuments extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_documents';
    public $timestamps = true;
    protected $fillable = ['candidate_id', 'rec_job_document_allocation_id', 'file_name'];


    public function documentJobAllocation()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecJobDocumentAllocation', 'rec_job_document_allocation_id', 'id');
    }
}

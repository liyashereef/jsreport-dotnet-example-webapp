<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecJobDocumentAllocation extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_job_document_allocations';
    public $timestamps = true;
    protected $fillable = ['document_id', 'job_id', 'process_tab_id', 'display', 'is_mandatory'];

    public function documentAllocation()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecDocumentAllocation', 'document_id', 'id');
    }

    public function documentAllocationWithTrashed()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecDocumentAllocation', 'document_id', 'id')->withTrashed();
    }

    public function documentJob()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateDocuments', 'rec_job_document_allocation_id', 'id');
    }
    
    //process tab
    public function processTab()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecProcessTab', 'process_tab_id', 'id');
    }
}

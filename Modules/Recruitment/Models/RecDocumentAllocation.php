<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecDocumentAllocation extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_document_allocations';
    public $timestamps = true;

    protected $fillable = [
        'process_tab_id',
        'document_name',
        'document_id',
        'order',
        'customer_id',
    ];

    //On boarding Document allocation
    public function onBoardingDocumentAllocation()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecOnboardingDocuments', 'document_id', 'id');
    }

    //process tab
    public function processTab()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecProcessTab', 'process_tab_id', 'id');
    }

    //job document allocation
    public function jobDocumentAllocation()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecJobDocumentAllocation', 'document_id', 'id');
    }

}

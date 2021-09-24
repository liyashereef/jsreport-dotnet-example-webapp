<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecOnboardingDocuments extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_onboarding_documents';
    public $timestamps = true;
    protected $fillable = ['document_name'];

    public function attachments()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecOnboardingDocumentAttachments', 'document_id', 'id');
    }
}

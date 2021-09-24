<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecOnboardingDocumentAttachments extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_onboarding_document_attachments';
    public $timestamps = true;
    protected $fillable = ['document_id','file_name','file_type'];
}

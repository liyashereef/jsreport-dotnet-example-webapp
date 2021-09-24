<?php

namespace Modules\Documents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['id','document_type_id','customer_id','user_id','other_category_lookup_id','document_category_id','other_category_name_id','document_name_id','answer_type','document_expiry_date','document_description','attachment_id','is_archived','created_by'];

    public function documentCategory()
    {
        return $this->belongsTo('Modules\Admin\Models\DocumentCategory','document_category_id','id')->withTrashed();
    }

    public function documentName()
    {
        return $this->belongsTo('Modules\Admin\Models\DocumentNameDetail','document_name_id','id')->withTrashed();
    }

    public function documentType()
    {
        return $this->belongsTo('Modules\Admin\Models\DocumentType','document_type_id','id')->withTrashed();
    }

    public function securityClearance()
    {
        return $this->belongsTo('Modules\Admin\Models\SecurityClearanceLookup','document_name_id','id')->withTrashed();
    }

    public function certificateMaster()
    {
        return $this->belongsTo('Modules\Admin\Models\CertificateMaster','document_name_id','id')->withTrashed();
    }


    public function projectDetails()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer','customer_id','id')->withTrashed();
    }

    public function UserDetails()
    {
        return $this->belongsTo('Modules\Admin\Models\User','user_id','id')->withTrashed();
    }
    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id')->withTrashed();
    }
    public function createdBy()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'id', 'created_by')->withTrashed();
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCategory extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['id','document_type_id','document_category','created_at','updated_at'];
    
    public function documentType()
    {
        return $this->belongsTo('Modules\Admin\Models\DocumentType','document_type_id','id')->withTrashed();
    }
    public function answerable()
    {
        return $this->morphMany('Modules\Admin\Models\DocumentNameDetail')->withTrashed();
    }
}

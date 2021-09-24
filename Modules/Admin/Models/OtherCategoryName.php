<?php

namespace Modules\Admin\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherCategoryName extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['id','name','document_type_id','other_category_lookup_id'];

    public function documentTypes()
    {
        return $this->belongsTo('Modules\Admin\Models\DocumentType', 'document_type_id', 'id');
    }
  
    public function createdBy()
    {
        return $this->hasOne('Modules\Admin\Models\User', 'created_by','id')->withTrashed();
    }
    
    public function otherCategory()
    {
        return $this->belongsTo('Modules\Admin\Models\OtherCategoryLookup', 'other_category_lookup_id', 'id');
    }

    public function name()
    {
        return $this->hasOne('Modules\Documents\Models\Document', 'other_category_name_id', 'id');
    }
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentNameDetail extends Model
{
    use SoftDeletes;

    public $timestamps = true;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['id','name','document_type_id','document_category_id','other_category_name_id','answer_type','is_valid','is_auto_archive'];

     /**
     * The Document Type that belongs to document name detail
     *
     */
    public function documentTypes()
    {
        return $this->belongsTo('Modules\Admin\Models\DocumentType', 'document_type_id', 'id');
    }
    /**
     * The Document category that belongs to document name detail
     *
     */
    public function documentCategories()
    {
        return $this->belongsTo('Modules\Admin\Models\DocumentCategory', 'document_category_id', 'id');
    }

    public function answerable()
    {
        //morphTo(string $name = null, string $type = null, string $id = null, string $ownerKey = null)
        return $this->morphTo('answerable', 'answer_type', 'document_category_id');

    } 
    public function AuthorizedAccessDetails()
    {
        return $this->hasMany('Modules\Admin\Models\DocumentAccessPermission','document_name_id', 'id');
    }
}

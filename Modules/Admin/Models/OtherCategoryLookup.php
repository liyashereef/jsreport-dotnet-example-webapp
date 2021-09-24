<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherCategoryLookup extends Model
{   
    use SoftDeletes;
    protected $fillable = ['id','category_name','shortname'];
    protected $dates = ['deleted_at'];

public function otherCategoryname()
{
    return $this->belongsTo('Modules\Admin\Models\OtherCategoryName', 'id','other_category_lookup_id')->withTrashed();
}
public function answerable()
    {
        //morphMany(string $related, string $name, string $type = null, string $id = null, string $localKey = null)
        //return $this->morphMany('Modules\CapacityTool\Models\DocumentNameDetail', 'answer_type','document_category_id');
        return $this->morphMany('Modules\Admin\Models\DocumentNameDetail')->withTrashed();
        //return $this->morphMany('Modules\CapacityTool\Models\CapacityTool', 'answer','answer_type','answer','id');
    }
}
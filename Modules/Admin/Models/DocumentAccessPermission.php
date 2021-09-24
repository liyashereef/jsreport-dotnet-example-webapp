<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DocumentAccessPermission extends Model
{
	use SoftDeletes;
    protected $fillable = ['document_name_id','access_id'];
    protected $dates = ['deleted_at'];

    public function AuthorisedAccessName()
    {
        return $this->belongsTo('Modules\Admin\Models\AuthorisedAccessDocument', 'access_id', 'id');
    }
}

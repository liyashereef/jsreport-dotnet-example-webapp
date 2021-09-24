<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthorisedAccessDocument extends Model
{

    /**
     * Relation to template Form
     *
     * @return type
     */
    use SoftDeletes;

    public $table = 'authorised_access_document_managements';
    protected $fillable = ['name','permission_id'];
    protected $dates = ['deleted_at'];
    
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpCatalogueGroup extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['group','created_by','updated_by'];
       
    protected $dates = ['deleted_at'];

   

}

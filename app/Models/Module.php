<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['name', 'parent_module_id'];

    /**
     * Relation to Module Permission
     *
     * @return type
     */
    public function permission_model()
    {
        return $this->hasMany('App\Models\ModulePermission', 'module_id', 'id')->where('status','=',ACTIVE)->orderBy('sequence_number','asc');
    }
}

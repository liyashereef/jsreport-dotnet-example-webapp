<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAllocation extends Model {

    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['user_id', 'supervisor_id', 'from', 'to', 'created_by', 'updated_by'];

    /**
     * Relation to user table - employee
     * @return type
     */
    public function user() {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

    /**
     * Relation to user table  - reporting officer
     * @return type
     */
    public function supervisor() {
        return $this->belongsTo('Modules\Admin\Models\User', 'supervisor_id', 'id');
    }

    public function CustomerEmployeeAllocation(){
        return $this->hasMany('Modules\Admin\Models\CustomerEmployeeAllocation', 'user_id','user_id');  
    }

}

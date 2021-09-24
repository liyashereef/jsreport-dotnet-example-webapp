<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentEmployees extends Model
{
    use SoftDeletes;

    protected $fillable = ['department_master_id', 'user_id'];
    public function user()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
        //
    }
}

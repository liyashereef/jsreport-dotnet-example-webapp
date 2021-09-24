<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentMaster extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','allocated_regionalmanager','allocated_supervisor'];

    public function employeeMapping()
    {
        return $this->hasMany('Modules\Admin\Models\DepartmentEmployees','department_master_id', 'id');

    }

}

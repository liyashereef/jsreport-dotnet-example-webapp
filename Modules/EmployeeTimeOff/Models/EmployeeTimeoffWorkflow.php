<?php

namespace Modules\EmployeeTimeOff\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EmployeeTimeOffWorkflow extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    public $table = 'employee_timeoff_workflow';
    protected $fillable = ['emp_role_id', 'level', 'approver_role_id', 'email_notification', 'created_at', 'updated_at'];

    

   

}

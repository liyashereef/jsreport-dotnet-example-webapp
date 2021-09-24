<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCustomer extends Model
{
    use SoftDeletes;
    public $timestamps = true;    
    protected $fillable = ['user_id','customer_id','start_date','active'];
}

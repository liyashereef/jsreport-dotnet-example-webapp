<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerQrcodeHistory extends Model
{
    protected $fillable = ['shift_id','scanned','missed'];

    
}

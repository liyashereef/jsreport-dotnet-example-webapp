<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class EmailAccountsMaster extends Model
{
    protected $fillable = ['display_name','email_address','user_name','password','smtp_server','port','encryption','default'];
}

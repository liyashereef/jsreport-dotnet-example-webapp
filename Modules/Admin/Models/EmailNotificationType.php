<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class EmailNotificationType extends Model
{
    use SoftDeletes;
    protected $fillable = ['type','display_name','customer_based','requester_based'];
    protected $dates = ['deleted_at'];
}

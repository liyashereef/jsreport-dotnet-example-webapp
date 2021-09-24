<?php

namespace Modules\IdsScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsPaymentMethods extends Model
{
    use SoftDeletes;
    protected $fillable = ['short_name','full_name','active','not_removable'];
    
}

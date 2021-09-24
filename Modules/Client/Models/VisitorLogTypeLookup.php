<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogTypeLookup extends Model
{
    use SoftDeletes;
    public $table = 'visitor_log_type_lookups';
}

<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ContractualVisitUnitLookup extends Model
{
    use SoftDeletes;
    protected $table = "contractual_visit_unit_lookups";
    protected $fillable = [
        'value',
    ];
    protected $dates = ['deleted_at'];

    
}

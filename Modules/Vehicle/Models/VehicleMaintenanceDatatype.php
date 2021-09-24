<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenanceDatatype extends Model
{
    use SoftDeletes;

 
    protected $fillable = ['name','shortname','short_description'];
}

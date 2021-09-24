<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenanceCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_name','tax'];
}

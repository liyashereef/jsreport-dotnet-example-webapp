<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleVendorLookup extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['vehicle_vendor'];
}

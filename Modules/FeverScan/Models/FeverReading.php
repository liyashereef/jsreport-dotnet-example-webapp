<?php

namespace Modules\FeverScan\Models;
use Illuminate\Database\Eloquent\SoftDeletes;


use Illuminate\Database\Eloquent\Model;

class FeverReading extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_id','shift_id','module_id','name','email','phone','gender','age_group','province','city','temperature_id','temperature','notes',
    'geo_location_lat','geo_location_long','created_by'];

     /**
     * The customer that belongs to fever reading
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }


}

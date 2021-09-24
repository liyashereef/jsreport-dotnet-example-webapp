<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerQrcodeLocation extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'customer_id', 'qrcode', 'location', 'no_of_attempts', 'created_by',
        'updated_by', 'qrcode_active', 'picture_enable_disable', 'picture_mandatory', 'location_enable_disable',
        'no_of_attempts_week_ends', 'tot_no_of_attempts_week_day', 'tot_no_of_attempts_week_ends',
    ];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }
}

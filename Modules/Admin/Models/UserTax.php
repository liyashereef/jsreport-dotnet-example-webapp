<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserTax extends Model
{
    use SoftDeletes;
    protected $table = 'user_tax';

    protected $fillable = ['user_id','federal_td1_claim','provincial_td1_claim','is_cpp_exempt','tax_province','epaystub_email','is_epaystub_exempt','created_by','updated_by','is_uic_exempt'];
    protected $dates = ['created_at','updated_at','deleted_at'];

    /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id'); //
    }
}

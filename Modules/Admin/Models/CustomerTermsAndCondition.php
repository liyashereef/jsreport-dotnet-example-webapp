<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTermsAndCondition extends Model
{
    use SoftDeletes;

    public $table = 'customer_terms_and_conditions';
    protected $fillable = ['customer_id','type_id','terms_and_conditions','created_by','updated_by'];

     /**
     * The user that belongs to employee allocation
     *
     */
    public function created_by()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id');
    }

    /**
     * The customer that belongs to employee allocation
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

}

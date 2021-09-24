<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRating extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer_id', 'rating_id', 'notes', 'created_by', 'updated_by'];

    /**
     * The customer that belongs to customer rating
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    /**
     * The user that belongs to customer rating
     *
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\user', 'created_by', 'id')->with('roles')->withTrashed();
    }

}

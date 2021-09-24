<?php

namespace Modules\ProjectManagement\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PmRatingTolerance extends Model
{

	use SoftDeletes;
    public $timestamps = true; 
   
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['rating_id','max_value'];
      /**
     * The customer details that belongs to project management
     *
     */
    public function rating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'rating_id', 'id');
    }
}
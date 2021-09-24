<?php

namespace Modules\ProjectManagement\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PmTaskUpdateInterval extends Model
{

	use SoftDeletes;
    public $timestamps = true; 
   
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['interval'];
    
}
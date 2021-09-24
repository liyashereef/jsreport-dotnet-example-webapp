<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['position'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function CpidLookUpWithTrashed(){
        return $this->hasMany(CpidLookup::class,'position_id','id')->withTrashed();
    }
   
}

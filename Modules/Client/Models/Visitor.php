<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use SoftDeletes;
    public static $snakeAttributes = false;
    protected $fillable = ['customerId','uid','barCode','firstName','lastName','email','phone',
    'visitorTypeId','avatar','visitorStatusId','notes','created_at','updated_at','deleted_at'];

    /**
     * Type relation
     */
    public function visitorType()
    {
        return $this->belongsTo('Modules\Client\Models\VisitorLogTypeLookup', 'visitorTypeId', 'id')->withTrashed();
    }
     /**
     * Customer relation
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customerId', 'id')->withTrashed();
    }
      /**
     * Customer relation
     */
    public function visitorStatus()
    {
        return $this->belongsTo('Modules\Admin\Models\VisitorStatusLookups', 'visitorStatusId', 'id')->withTrashed();
    }
}

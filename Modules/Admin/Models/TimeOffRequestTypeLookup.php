<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeOffRequestTypeLookup extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['request_type', 'color', 'is_deletable'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get all of the comments for the TimeOffRequestTypeLookup
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeOffSettings()
    {
        return $this->hasMany(
            TimeOffRequestTypeSetting::class,
            'time_off_request_type_id'
        );
    }
}

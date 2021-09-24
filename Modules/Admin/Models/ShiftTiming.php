<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftTiming extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['shift_name', 'display_name', 'from', 'to', 'displayable'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

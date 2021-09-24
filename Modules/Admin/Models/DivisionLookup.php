<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionLookup extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    public $table = 'division_lookups';
    protected $fillable = ['division_name'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

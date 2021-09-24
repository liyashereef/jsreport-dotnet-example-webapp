<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationCentreEmail extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $table = 'operations_centre_email';
    protected $fillable = ['email'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}

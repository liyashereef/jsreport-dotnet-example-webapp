<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateMaster extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['certificate_name', 'is_deletable'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}

<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecSecurityAwareness extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    public $timestamps = true;
    protected $table = 'rec_security_awareness';
    protected $fillable = ['answer','order_sequence'];
     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

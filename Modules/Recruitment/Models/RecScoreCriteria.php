<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecScoreCriteria extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    public $timestamps = true;
    protected $fillable = [
        'criteria_name',
        'type_id'
    ];
}
